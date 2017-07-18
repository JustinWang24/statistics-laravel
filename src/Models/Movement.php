<?php
/**
 * Created by PhpStorm.
 * User: justinwang
 * Date: 18/7/17
 * Time: 10:28 AM
 */

namespace Newflit\Statistics\Models;

use Illuminate\Database\Eloquent\Model as LaravelEloquentModel;

class Movement extends LaravelEloquentModel
{
    protected $table = 'nf_statistic_movements';

    protected $fillable = [
        'visitor_id',
        'user_id',
        'ip',
        'language',
        'url',
        'uri',
        'method',
        'referrer',
        'redirect_to',
        'lat',
        'lng',
        'post_data_in_json',
        'get_data_in_json',
        'country',
        'state',
        'city',
        'user_id',
        'year',
        'month',
        'week',
        'day',
        'hour',
        'minute',
    ];

    /**
     * Get by uuid in Cookie
     * @param string $visitorId
     * @return Visitor
     */
    public static function GetLastByVisitorId($visitorId){
        if($visitorId){
            // Get the latest one according to the visitor ID
            return self::where('visitor_id',$visitorId)->latest()->first();
        }
        return false;
    }
}