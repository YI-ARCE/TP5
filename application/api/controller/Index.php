<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\common\module\Rule;
use think\Config;
use think\Log;
use think\Session;

class Index extends Api
{

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
        return Rule::rule(Session::get('userinfo')['user_authId']);
    }
}
