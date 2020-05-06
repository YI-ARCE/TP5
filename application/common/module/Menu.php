<?php


namespace app\common\module;


use think\Log;
use think\Model;

/**
 * sys_menu 功能表模型
 */
class Menu extends Model
{
    protected $table = 'sys_menu';

    private static $features = [];

    public static function getmenu($data){
        if($data == '*'){
            return Menu::all();
        }else{
            $set = explode(',',$data);
            foreach ($set as $key => $value){
                self::$features[$value-1] = Menu::get(['menu_id'=>$value])->data;
            }
            return self::$features;
        }

    }
}
