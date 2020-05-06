<?php
namespace app\api\controller;

use app\api\module\fc;
use app\common\controller\Api;
use app\common\module\Rule;
use think\Config;
use think\Log;
use think\Session;

class Index extends Api
{
    protected $ruleSet = [];

    /**
     * 接口主页
     * 返回API相关信息
     */
    public function index()
    {
        $config = Config::get('config');
        Log::log($config);
        $this->success('api连接成功',$config);
        return 0;
    }

    /**
     * 主页菜单
     * @return mixed
     */
    public function home()
    {
        $set = Rule::rule(Session::get('userinfo')['user_authId']);
        $this->ruleSet = fc::getMenuset($set);
        $this->success('查询成功!',Session::get('user_token'),201,$this->ruleSet);
    }

    /**
     * 控制台
     */
    public function console(){

    }

    /**
     * 常规管理
     * @param string $find 获得指定方法
     * @param string $feature 获得指定执行功能
     * @return bool|string
     */
    public function regular($find,$feature = null){
        switch ($find){
            case 'userinfo':
                if ($feature == null){
                    $this->success('响应成功',null,200,$this->jtw->getToken(['data'=>Session::get('user_info')]));
                }else{
                    $res = fc::getUserinfo($feature,input('data/a'));
                    $this->success($res,Session::get('user_token'),202);
                }
                break;
            case 'ordercheck':
                return '订单查看';
            case 'usermanage':
                return '用户管理';
            default:
                return view('error');
        }

    }

    /**
     * 系统管理
     */
    public function system(){

    }
}
