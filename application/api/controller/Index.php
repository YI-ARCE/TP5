<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\common\module\Rule;
use think\Config;
use think\Log;
use think\Session;

class Index extends Api
{
    protected $ruleSet = [];

    protected function getMenuset($set,$pid=null,$upid=null,$root = null){
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
                        'children'=>$this->getMenuset($set,$pid,$upid,1),
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
                        'children'=>$this->getMenuset($set,$pid,$upid,1),
                    ];
                    $id++;
                }
            }
        }
        if (empty($setcd)){
            $setcd = null;
        }
        Log::log($setcd);
        return $setcd;
    }

    /**
     * 接口主页
     * 返回API相关信息
     */

    public function index()
    {
        $config = Config::get('config');
        Log::log($config);
        $this->success('api连接成功', $config);
        return 0;
    }

    public function home()
    {
        $set = Rule::rule(Session::get('userinfo')['user_authId']);
        $this->ruleSet = $this->getMenuset($set);
        return $this->ruleSet;
    }
}
