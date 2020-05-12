<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Log;
use think\Request;
use think\Session;

/**
 * 用户操作类
 */
class User extends Api
{
    /**
     * 登录验证
     */
    public function login(){
        if (Request::instance()->isPost()==false && Request::instance()->isAjax()==false)
            $this->error('错误的请求方式!',500);
        $username = input('username');
        $password = input('password');
        if(!$username||!$password){
            $this->error('账号或密码不能为空',500);
        }
        $result = $this->auth->login($username,$password);
        if($result['bool']){
            if ($result['code'] ==201){
                $token = $this->jtw->getToken(['data'=>$result['data']['user_name'],'iat'=>time(),'exp'=>time()+60,'nbf'=>time()]);
                Session::set('userinfo',$result['data']);
                Session::set('user_token',$token);
                return ['code'=>201, 'time'=>time(),'msg'=>$result['msg'],'token'=>$token, 'data'=>['user_name'=>$result['data']['user_name']]];
            }else{
                return ['code'=>$result['code'], 'time'=>time(),'msg'=>$result['msg']];
            }
        }else{
            $this->error($result['msg'],$result['code']);
        }
        return 0;
    }
    public function logout(){
        Session::clear();
        $this->success('账户已退出！',210,null);
    }
}