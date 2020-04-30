<?php


namespace app\common\module;
use think\Log;

class Encryption
{
    public static $field = '';
    public static function enc($field1,$salt){
        self::$field = substr_replace($field1,$salt,3,0);
        return hash('sha512',self::$field);
//        $this->field = substr_replace($field,$salt,3);
//        Log::log(hash('sha512',$this->field));
//        return hash('sha512',$this->field);
    }
}