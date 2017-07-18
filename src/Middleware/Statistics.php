<?php
namespace Newflit\Statistics\Middleware;

use Carbon\Carbon;
use Closure;
use Log;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request as LaravelRequest;
use Cookie;
use Auth;
use Jenssegers\Agent\Agent;
use Ramsey\Uuid\Uuid;
use Newflit\Statistics\Models\Visitor;
use Newflit\Statistics\Models\Movement;
use Newflit\Statistics\ViewLoader;

class Statistics
{
    protected $container;

    protected $viewLoader;

    protected $loginUserId = 0;           // The current login user ID

    protected $userAgentDataArray = null; // User Agent Data

    private $agentDetector = null;        // User Agent Detector

    private $uuidInCookie = null;         // Uuid which will be sent by cookie to client

    private $isFirstTime = true;          // If first time coming.  表示是否第一次访问

    /**
     * Create a new middleware instance.
     *
     * @param  Container $container
     * @param  ViewLoader $viewLoader
     */
    public function __construct(Container $container, ViewLoader $viewLoader)
    {
        $this->container = $container;
        $this->viewLoader = $viewLoader;
    }

    /**
     * Handle an incoming request.
     *
     * @param  LaravelRequest  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(LaravelRequest $request, Closure $next)
    {
        // todo: 从 cookie 中取 uuid
        $this->uuidInCookie = $request->cookie(config('statistics.cookie_identifier_name'));

        // Determine if this one need to be recorded
        if($this->getAgentDetector()->robot()){
            // Ignored if a robot
            return $next($request);
        }

        if($this->ignoreThisOne($request) && $this->uuidInCookie){
            // 在忽略路径中并且已经设置过 cookie 了
            return $next($request);
        }

        // Can't be ignored  无法忽略的
        $this->userAgentDataArray = $this->parseAgent();  // Parse the user agent

        // Init the login user id
        if(config('statistics.use_laravel_auth')){
            // If use Laravel Auth
            $id = Auth::id();
            $this->loginUserId = $id ? $id : 0;
        }else{
            $this->loginUserId = $request->session()->has(config('statistics.login_user_data_session_key')) ?
                $request->session()->get(config('statistics.login_user_data_session_key')) : 0;
        }

        // Save the visitor's detail into database
        $this->persistentVisitor($request,$next);

        $response = $next($request);
        if($this->isFirstTime){
            // 通过 Cookie 保存到客户端
            $cookie = Cookie::forever('uuid', $this->uuidInCookie);

            /*
             * At first time, send a cookie to let user tell the server its screen size
             * 第一次访问的时候, 设置一个 cookie. 指示要发送用户的屏幕尺寸到服务器端.
             * 默认 Laravel 的 cookie 在浏览器端是取不到的. 下面的方法, 设置 cookie, 以便可以被取到
             */
            $cookieNeedScreenSize = Cookie::make(
                'nf_need_screen_size',
                1,
                10,
                config('path.path'),
                config('session.domain'),
                config('session.secure'),
                false
            );

            $response->headers->setCookie($cookie);
            $response->headers->setCookie($cookieNeedScreenSize);
            $this->viewLoader->insertPushScreenSizeJs($response);
        }

        return $response;
    }

    /**
     * Persistent visitor into database
     * @param LaravelRequest $request
     * @param Closure $next
     * return void
     */
    protected function persistentVisitor(LaravelRequest $request, Closure $next){
        // Try to get the last one from the database
        $agentData = $this->userAgentDataArray;

        if($this->uuidInCookie) {
            // 不是第一次访问了
            $this->isFirstTime = false;
            $visitorId = Visitor::GetByUuid($this->uuidInCookie);
            if($visitorId){
                $this->createNewMovement($request, $visitorId);
            }
        }else{
            // 第一次访问
            $this->uuidInCookie = Uuid::uuid4()->toString();

            $visitorData = [
                'device_type'           => $agentData['device_type'],
                'device_name'           => $agentData['device_name'],
                'user_agent'            => $agentData['user_agent'],
                'user_agent_version'    => $agentData['user_agent_version'],
                'user_os'               => $agentData['user_os'],
                'screen_width'          => 0,
                'screen_height'         => 0
            ];
            $visitor = Visitor::create($visitorData);

            // 这里使用一个小的技巧, 以便以后查询的时候可以更快, 把主键附加的 uuid 后面在发给客户端
            if($visitor){
                $this->uuidInCookie .= '___' . $visitor->id;
                // 保存访客的第一次 Movement
                $this->createNewMovement($request);
            }
        }
    }

    /**
     * Create a new movement record in database
     * @param LaravelRequest $request
     * @param int $visitorId
     */
    private function createNewMovement(LaravelRequest $request, $visitorId = 0){
        $dateTime = Carbon::now(config('app.timezone'));

        $movementData = [
            'language'              =>$this->userAgentDataArray['language'],
            'visitor_id'            =>$visitorId,
            'user_id'               =>$this->loginUserId,    // 第一次来, 肯定是不可能登陆的. 所以是0. 登陆之后才可能不是0
            'ip'                    =>$request->ip(),
            'url'                   =>$request->fullUrl(),
            'uri'                   =>$request->getRequestUri(),
            'method'                =>$request->method(),
            'referrer'              =>$request->headers->get('referer').'',
            'lat'                   =>null,
            'lng'                   =>null,
            'post_data_in_json'     =>null,
            'get_data_in_json'      =>null,
            'country'               =>null,
            'state'                 =>null,
            'city'                  =>null,
            'year'                  =>$dateTime->year,
            'month'                 =>$dateTime->month,
            'week'                  =>$dateTime->weekOfYear,
            'day'                   =>$dateTime->day,
            'hour'                  =>$dateTime->hour,
            'minute'                =>$dateTime->minute
        ];
        Movement::create($movementData);
    }

    /**
     * Get the Agent Detector
     * @return Agent|null
     */
    public function getAgentDetector(){
        if(!$this->agentDetector){
            $this->agentDetector = new Agent();
        }
        return $this->agentDetector;
    }

    /**
     * Parse Visitor's browser
     * 解析出用户浏览器的完成信息
     * @return array
     */
    private function parseAgent(){
        $agent = $this->getAgentDetector();
        $agentData = [];

        $agentData['device_type'] = $agent->isDesktop() ? config('statistics.user.agent.type.DESKTOP') :
            ($agent->isPhone() ? config('statistics.user.agent.type.MOBILE') : config('statistics.user.agent.type.TABLET'));

        $agentData['device_name'] = $agent->device();
        $agentData['user_agent'] = $agent->browser();
        $agentData['user_os'] = $agent->platform();
        if($agent->isMobile()){
            $agentData['user_agent_version'] = $agent->version($agentData['user_agent']);
        }else{
            $agentData['user_agent_version'] = $agent->version($agentData['user_os']);
        }
        $agentData['language'] = implode(',',$agent->languages());

        return $agentData;
    }

    /**
     * Determine the given request shall be ignore or not, base on the config
     * 查一下给定的 request 是否需要被忽略
     * @param LaravelRequest $request
     * @return bool
     */
    private function ignoreThisOne(LaravelRequest $request){
        $answer = false;
        $ignores = config('statistics.ignores');

        // Check if the method is in the ignore list
        if(isset($ignores[$request->method()])){
            $ignores[$request->method()];
            $requestUri = $request->getRequestUri();
            if(!empty($ignores[$request->method()]['uri_names'])){
                $answer = in_array($requestUri, $ignores[$request->method()]['uri_names']);
            }

            if(!$answer){
                // Check Group
                $temp = explode('/',$requestUri);
                if(count($temp)>1){
                    $answer = in_array($temp[1], $ignores[$request->method()]['group_names']);
                }
            }
        }

        return $answer;
    }
}