<?php


namespace app\api\module;
use \app\api\controller\Index;
use app\common\module\User;
use Exception;
use think\Log;
use think\Session;

class fc extends Index
{
    /**
     * 获得主页菜单列
     * @param array $set 所有功能集
     * @param int $pid 功能或菜单父ID
     * @param int $upid 功能或菜单上级ID
     * @param int $root 是否多次循环
     * @return array|null 返回当前功能或菜单信息|是否多次循环
     */
    public static function getMenuset($set,$pid=null,$upid=null,$root = null){
        $id = 0;
        $setcd = [];
        if($root == null){
            foreach ($set as $key => $value){
                //根菜单优先判断
                if($value['menu_type'] == 1 && $value['menu_pid'] == 0 && $value['menu_upid'] == 0){
                    $pid = $value['menu_id'];
                    $upid = $value['menu_id'];
                    $setcd[$id] = [
                        'authname'=>$value['menu_name'],
                        'authpath'=>$value['menu_path'],
                        'authid' =>$value['menu_id'],
                        'authstatus'=>$value['menu_status'],
                        'children'=>self::getMenuset($set,$pid,$upid,1),
                    ];
                    $id++;
                }
            }
        }else{
            foreach ($set as $key => $value){
                if($value['menu_pid'] != $pid){
                    continue;
                }
                if($value['menu_upid'] != $upid){
                    continue;
                }
                if ($value['menu_type']==2){
                    //确定该类型为功能，不添加菜单索引
                    $setcd[$id] = [
                        'authname'=>$value['menu_name'],
                        'authpath'=>$value['menu_path'],
                        'authid' =>$value['menu_id'],
                        'authstatus'=>$value['menu_status'],
                    ];
                    $id++;
                }else{
                    //确定该类型为子菜单
                    $upid = $value['menu_id'];
                    $setcd[$id] = [
                        'authname'=>$value['menu_name'],
                        'authpath'=>$value['menu_path'],
                        'authid' =>$value['menu_id'],
                        'authstatus'=>$value['menu_status'],
                        'children'=>self::getMenuset($set,$pid,$upid,1),
                    ];
                    $id++;
                }
            }
        }
        if (empty($setcd)){
            $setcd = null;
        }
        return $setcd;
    }

    /**
     * @param int $fun 增删改查
     * @param null $data 需要的数据
     * @return string
     * @throws Exception
     */
    public static function getUserinfo($fun,$data = null){
        switch ($fun){
            //修改个人信息
            case 1:
                $save = User::get(['user_name'=>$data['user_name']]);
                unset($data['user_name']);
                $save->save($data);
                return '修改成功';
            //头像上传
            case 2:
                break;
        }
    }
}