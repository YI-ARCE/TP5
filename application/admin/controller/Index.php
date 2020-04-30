<?php
namespace app\admin\controller;

use think\Model;
use think\Validate;
use think\Log;
use think\Db;
class Index
{
    public function admin(){
        return view('index1');
    }
    public function login(){
        $username = input('username');
        $password = input('password');
        $data2 = input();
        $keeplogin = input('keeplogin');
        $token = input('token');
        $rule = [
            'username'=>'require|length:6,11',
            'password'=>'require|length:6,18',
        ];
        $data = [
            'username'=>$username,
            'password'=>$password,
        ];
        $validate = new Validate($rule);
        $result = $validate->check($data);
        if(!$result){
            Log::log($data2);
            Log::log($validate->getError());
            return 0;
        }else{
            $json = [
                'code'=>20000,
                'username'=>$username,
                'data'=>['token'=>'caonimalegebi']
            ];
            return json_encode($json);
        }
    }
    public function register(){
        $username = input('username');
        $password = input('password');
        $data2 = input();
        $keeplogin = input('keeplogin');
        $token = input('token');
        $rule = [
            'username'=>'require|length:6,11',
            'password'=>'require|length:6,18',
        ];
        $data = [
            'username'=>$username,
            'password'=>$password,
        ];
        $validate = new Validate($rule);
        $result = $validate->check($data);
        if(!$result){
            Log::log($data2);
            Log::log($validate->getError());
            return 0;
        }else{
            $ddd = [
                'user_username'=> $username,
                'user_password'=> $password,
                'user_salt'=> 'asd123',
                'user_create_time'=> time(),
                'user_status'=>1,
            ];
            $yiarce = new Db;
            $yiarce::table('sys_user')->insert($ddd);
            return 1;
        }
    }

}
