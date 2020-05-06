<?php


namespace app\common\module;


use think\Log;
use think\Model;

class Rule extends Model
{
    /**
     * 权限表模型
     * @var string
     */
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