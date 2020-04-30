<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\module\Devicelog;
use app\common\module\Loginlog;
use app\common\module\Rule;
use think\Log;
use think\Request;
use think\Session;

class User extends Api
{
    /**
     * 登录验证
     */
    public function login(){
        if (Request::instance()->isPost()==false && Request::instance()->isAjax()==false)
            $this->error('错误的请求方式!',[],500);
        $username = input('username');
        $password = input('password');
//        $device = input('device');
        if(!$username||!$password){
            $this->error('账号或密码不能为空',[],500);
        }
        $result = $this->auth->login($username,$password);
        if($result['bool']){
            $data = $result['data'];
            if ($result['code'] ==201){
                Loginlog::loginlog(['user_id'=>$result['data']['user_id'],
                    'lg_ip'=>$this->request->ip(),'lg_status'=>$result['code'],
                    'lg_addData'=>time(),
                    'lg_device'=>'ceshishebei']);
                Session::set('userinfo',$result['data']);
//                $features = Rule::rule($data['user_authId']);
//                $data['features'] = $features;
                $data = $this->jtw->getToken(['data'=>$result['data']['user_name'],'iat'=>time(),'exp'=>time()+60,'nbf'=>time()]);
                Session::set('user_token',$data);
            }
            $this->success($result['msg'],$data,$result['code'],['user_name'=>$result['data']['user_name']]);
        }else{
            if($result['code']== 404 || $result['code']== 400){
                $this->error($result['msg'],[],$result['code']);
            }else{
                Loginlog::loginlog(['user_id'=>$result['data']['user_id'],
                    'lg_ip'=>$this->request->ip(),'lg_status'=>$result['code'],
                    'lg_addData'=>time(),
                    'lg_device'=>'ceshishebei']);
                $this->error($result['msg'],[],$result['code']);
            }

        }
    }
    public function autologin(){
        if (Session::has('user_token')){
            $this->success('您已经登录了！',['data'=>Session::get('user_token')],204);
        }else{
            $this->error('还没有登录!',[],405);
        }
    }
}