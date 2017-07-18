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
     * @param string $uuidInCookie
     * @return Visitor
     */
    public static function GetByUuid($uuidInCookie){
        if($uuidInCookie){
            // 由于保存的 uuid 实际是 uuid + ___ + 真实 id, 所以取出真实ID 的值才能查找的更快
            $temp = explode('___',$uuidInCookie);
            if(count($temp) === 2)
                return self::find($temp[1]);
        }
        return false;
    }
}