<?php


namespace app\common\module;


use think\Log;
use think\Model;

class Menu extends Model
{
    protected $table = 'sys_menu';
    private static $features = [];
    public static function getmenu($data){
        if($data == '*'){
            Log::log('走的这里');
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
