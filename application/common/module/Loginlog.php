<?php


namespace app\common\module;


use think\Model;

class Loginlog extends Model
{
    protected $table = 'sys_user_login';

    private static $lg_info;

    public static function loginlog($date){
        self::$lg_info = $date;
        $save = new Loginlog();
        $save->data($date);
        $save->save();
    }
}