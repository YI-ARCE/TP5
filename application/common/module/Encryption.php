<?php


namespace app\common\module;
use think\Log;

class Encryption
{
    /**
     * @var string 获取密码合并盐值的字符串
     */
    public static $field = '';

    /**
     * 密码加密
     * @param string $field1 用户密码
     * @param string $salt 盐值
     * @return string 返回加密后的密钥
     */
    public static function enc($field1,$salt){
        self::$field = substr_replace($field1,$salt,3,0);
        return hash('sha512',self::$field);
//        $this->field = substr_replace($field,$salt,3);
//        Log::log(hash('sha512',$this->field));
//        return hash('sha512',$this->field);
    }
}