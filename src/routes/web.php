<?php
use Illuminate\Http\Request as LaravelRequest;
use Newflit\Statistics\Models\Visitor;
use Newflit\Statistics\Models\Movement;

Route::middleware(['web'])->group(function() {
    Route::get('/nf_push_screen_size',function(LaravelRequest $request){
        $visitor = Visitor::GetByUuid(
            $request->cookie(config('statistics.cookie_identifier_name')), true);

        if($request->has('sw') && $request->has('sh') && $visitor){
            $visitor->screen_width = $request->get('sw');
            $visitor->screen_height = $request->get('sh');
            if($visitor->save()){
                return 'success';
            }else{
                return 'fail';
            }
        }else{
            return 'fail';
        }
    })->name('nf_push_screen_size');

    Route::get('/nf_push_geo_location',function(LaravelRequest $request){
        /**
         * 这个调用是在页面加载之后立刻调用的, 所以应该去找最新的一条 Movement 记录并更新其中的内容
         * Get the latest movement record according to the visitor id then update lat and lng.
         */
        $visitorId = Visitor::GetByUuid(
            $request->cookie(config('statistics.cookie_identifier_name')));
        $movement = Movement::GetLastByVisitorId($visitorId);
        if($movement){
            $movement->lat = $request->get('lat');
            $movement->lng = $request->get('lng');
            if($movement->save()){
                return 'success';
            }else{
                return 'fail';
            }
        }
    })->name('nf_push_geo_location');
});