<?php


namespace app\common\library;



use app\common\module\User;
use think\Db;
use think\Log;
use think\Session;

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

    /**
     * 日志写入
     * @param array $actionData 保存的数据
     * @return int
     */
    public static function adminActionLog($actionData){
        $db = new Db();
        $userId = Session::get('userinfo')['user_id'];
        $uri = substr(request()->url(),4);
        Log::key($uri);
        $menu_id = $db::table('sys_menu')->where('menu_path',$uri)->find()['menu_id'];

        if ($menu_id != null)
        {
            $db::table('sys_action')->insert(['user_id'=>$userId,'menu_id'=>$menu_id,'ac_data'=>json_encode($actionData),'ac_uri'=>$uri]);
        }
        return 0;
    }
}