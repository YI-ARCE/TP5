<?php


namespace app\common\module;


use think\Log;
use think\Model;
use think\Session;

class Rule extends Model
{
    protected $table = 'sys_auth';
    private static $features = [];
    public static function rule($data){
        $rule = $data;
        $set = Rule::get(['id'=>$rule]);
        $setid = $set->data['au_features'];
        self::$features = Menu::getmenu($setid);
        return self::$features;
    }
}