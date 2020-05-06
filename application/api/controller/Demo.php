<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Request;
use think\Session;

/**
 * 用户操作类
 */
class Demo extends Api
{
    /**
     * 登录验证
     */

    public function login(){
        if (Request::instance()->isPost()==false && Request::instance()->isAjax()==false)
            $this->error('错误的请求方式!',[],500);
        $username = input('username');
        $password = input('password');
        if(!$username||!$password){
            $this->error('账号或密码不能为空',[],501);
        }
        $result = $this->auth->login($username,$password);
        if($result['bool']){
            $data = $result['data'];
            if ($result['code'] ==201){
                $data = $this->jtw->getToken(['data'=>$result['data']['user_name'],'iat'=>time(),'exp'=>time()+60,'nbf'=>time()]);
                Session::set('userinfo',$result['data']);
                Session::set('user_token',$data);
            }
            $this->success($result['msg'],$data,$result['code'],['user_name'=>$result['data']['user_name']]);
        }else{
            if($result['code']== 404 || $result['code']== 400){
                $this->error($result['msg'],[],$result['code']);
            }else{
                $this->error($result['msg'],[],$result['code']);
            }

        }
    }
    public function logout(){
        Session::clear();
        $this->success('账户已退出！',[],210);
    }
}