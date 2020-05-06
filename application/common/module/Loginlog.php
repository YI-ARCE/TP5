<?php


namespace app\common\module;


use think\Model;

/**
 * sys_user_login 登录日志表模型
 */
class Loginlog extends Model
{
    protected $table = 'sys_user_login';

    /**
     * 登录日志
     * @param array $date 登录信息
     */
    public static function loginlog($date){
        $save = new Loginlog();
        $save->data($date);
        $save->save();
    }
}