<?php
/**
 * Created by PhpStorm.
 * User: justinwang
 * Date: 18/7/17
 * Time: 10:28 AM
 */

namespace Newflit\Statistics\Models;

use Illuminate\Database\Eloquent\Model as LaravelEloquentModel;

class Visitor extends LaravelEloquentModel
{
    protected $table = 'nf_statistic_visitors';

    protected $fillable = [
        'is_new',
        'is_login',
        'device_type',
        'cookie_value',
        'device_name',
        'user_agent',
        'user_agent_version',
        'screen_width',
        'screen_height',
        'user_os',
        'uuid'
    ];

    /**
     * Get by uuid in Cookie. 如果 $withRecord 为真则返回对象, 否则只返回 id 即可
     * @param string $uuidInCookie
     * @param bool $withRecord
     * @return Visitor/string/bool
     */
    public static function GetByUuid($uuidInCookie, $withRecord = false){
        if($uuidInCookie){
            // 由于保存的 uuid 实际是 uuid + ___ + 真实 id, 所以取出真实ID 的值才能查找的更快
            $temp = explode('___',$uuidInCookie);
            if(count($temp) === 2){
                return $withRecord ? self::find($temp[1]) : $temp[1];
            }
        }
        return false;
    }
}