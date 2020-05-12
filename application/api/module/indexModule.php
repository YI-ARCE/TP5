<?php


namespace app\api\module;
use \app\api\controller\Index;
use app\common\controller\Api;
use app\common\library\Sundry;
use app\common\module\Encryption;
use app\common\module\Jwt;
use app\common\module\User;
use Exception;
use think\Config;
use think\Log;
use think\Request;
use think\Session;
use think\Validate;

class indexModule extends Index
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
     * 常规管理功能
     * @param int $fun 增删改查
     * @param null $data 需要的数据
     * @return string
     * @throws Exception
     */
    public static function editUserinfo($fun,$data = null){
        $save = User::get(['user_name'=>Session::get('userinfo')['user_name']]);
        switch ($fun){
            //修改个人信息
            case 'edit':
                unset($data['user_name']);
                $save->save($data);
                return '修改成功';
            //头像上传
            case 'upload':
                $data = request()->file('image');
                $type = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
                $info = $data->move(ROOT_PATH . 'public' . DS . 'user'.'image',Session::get('userinfo')['user_name'].'.'.$type);
                if($info){
                    $save->user_head = $info->getSaveName();
                    $save->save();
                    return '图片上传成功';
                }else{
                    return '图片上传失败，请检查文件！';
                }
                break;
            default:
                return '错误的参数选择!';
        }
    }

    /**
     * 管理员操作功能
     * @param $fun
     * @return array
     */
    public static function admin($fun){
        $data = input();
        if(!is_array($data)){
            return ['msg'=>'传参错误','data'=>null,'type'=>'error'];
        }
        if(!array_key_exists('user_id',$data)){
            if (!array_key_exists('user_name',$data)){
                return ['msg'=>'请检查您的关键参数是否存在！','data'=>null,'type'=>'error'];
            }
        }
        switch ($fun){
            //添加新的管理员
            case 'add':
                return adminModule::addUser($data);
            //更改管理员信息
            case 'edit':
                return adminModule::editUser($data);
            //删除管理员
            case 'delete':
                return adminModule::deleteUser($data);
            //查找管理员
            case 'select':
                return adminModule::selectUser($data);
            //管理员状态修改
            case 'status':{
                return adminModule::changeStatus($data);
            }
            default:
                return ['msg'=>'不存在的功能模块!','data'=>$data,'type'=>'error'];
        }
    }
}