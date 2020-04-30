<?php


namespace app\common\module;


use think\Model;

class Devicelog extends Model
{
    protected $table = 'sys_device';

    private static $dv_info;

    public static function devicelog($date){
        self::$dv_info = $date;
        $save = new Devicelog();
        $save->data(self::$dv_info);
        $save->save();
    }
    public static function check($data){
        self::$dv_info = Devicelog::get(['user_id'=>$data]);
        if(self::$dv_info){
            if(self::$dv_info['user_id']==$data){
                if(self::$dv_info['dv_status'] == 1){
                    //该设备已经通过审核;
                    return 1;
                }
            }else{
                //该设备已经绑定其它帐号
                return 2;
            }
        }else{
            //不存在该设备,做记录后返回
            return 3;
        }
    }
}