<?php
/**
 * Created by PhpStorm.
 * User: justinwang
 * Date: 18/7/17
 * Time: 11:01 PM
 */

namespace Newflit\Statistics;

use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpFoundation\Response;

class ViewLoader
{
    /**
     * The Application Context
     * @var Application|\Illuminate\Foundation\Application|mixed|null
     */
    protected $app = null;

    /**
     * @param Application|null $app
     */
    public function __construct(Application $app = null)
    {
        if (!$app) {
            $app = app();   //Fallback when $app is not given
        }
        $this->app = $app;
    }

    public function insertPushScreenSizeJs(Response $response){
        $content = $response->getContent();
        /**
         * Check if the js file had been published. If yes, load the published js file.
         */
        if(file_exists(base_path('resources/views/vendor/statistics-laravel/nf_push_screen_size.js'))){
            $str = file_get_contents(base_path('resources/views/vendor/statistics-laravel/nf_push_screen_size.js'));
        }else{
            $str = file_get_contents(__DIR__.'/views/nf_statistics/nf_push_screen_size.js');
        }

        $response->setContent(str_replace('</body>','</body>'.$str,$content));
        return $response;
    }
}