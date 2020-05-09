<?php


namespace app\common\library;



use app\common\module\User;
use think\Log;

class Sundry
{
    /**
     * 随即生成字符串
     * @param $len
     * @param bool $special
     * @return string
     */
    public static function random($len,$special = false){
        $chars = ['a','b','c','d','e','f','g','h','i','j','k','l',
            'm','n','o','p','q','r','s','t','u','v','w','x','y','z',
            '0','1','2','3','4','5','6','7','8','9'];

        if ($special){
            $chars = array_merge($chars, [
                "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
                "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
                "}", "<", ">", "~", "+", "=", ",", "."
            ]);
        }
        $charlen = count($chars)-1;
        shuffle($chars);
        $str = '';
        for ($i=0;$i<$len;$i++){
            $str.=$chars[mt_rand(0,$charlen)];
        }

        return $str;
    }
}