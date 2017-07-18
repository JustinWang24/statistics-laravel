<?php
use Illuminate\Http\Request as LaravelRequest;
use Newflit\Statistics\Models\Visitor;


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
});