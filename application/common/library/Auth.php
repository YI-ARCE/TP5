<?php


namespace app\common\library;


use app\common\module\Devicelog;
use app\common\module\User;
use app\common\module\Encryption;
use think\Config;
use think\Log;
use think\Request;
use think\Validate;

class Auth
{
    protected static $instance = null;
    protected $config = [];
    protected $username = '';
    protected $field = '';
    protected $userinfo = [];

    public function __construct($options = [])
    {
        if ($config = Config::get('user')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->options = array_merge($this->config, $options);
    }

    /**
     *
     * @param array $options 参数
     * @return Auth
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * @param $username
     * @param $password
     * @return array|bool
     * @throws \think\exception\DbException
     */
    public function login($username, $password)
    {
        $rule = [
            'user_name' => 'require|alphaDash|length:6,11',
            'user_password' => 'require|length:6,18'
        ];
        $message = [
            'user_name' => '用户名或密码格式填写不正确',
            'user_password' => '用户名或密码格式填写不正确',
        ];
        $data = [
            'user_name' => $username,
            'user_password' => $password,
        ];
        $validate = new Validate($rule, $message);
        $result = $validate->check($data);
        if ($result) {
            $this->username = $username;
            $this->userinfo = User::get(['user_name' => $this->username]);
            if ($this->userinfo == null) {
                $result = [];
                $result['bool'] = false;
                $result['code'] = 404;
                $result['msg'] = '用户名或密码不正确请重新输入！';
                return $result;
            }
            $this->field = Encryption::enc($password, $this->userinfo['user_salt']);
            $result = [];
            if ($this->field == $this->userinfo['user_password']) {
                $result['bool'] = true;
                unset($this->userinfo['user_password']);
                $result['data'] = $this->userinfo;
                switch ($this->userinfo['user_status']) {
                    case 1:
                        $result['code'] = 201;
                        $result['msg'] = '登录成功';

                        break;
                    case 2:
                        $result['code'] = 202;
                        $result['msg'] = '您的账户被锁定，联系管理员解锁！';
                        break;
                    case 3:
                        $result['code'] = 203;
                        $result['msg'] = '第一次登录，请先完善个人信息';
                        break;
                }

            } else {
                $result = [];
                $result['code'] = 401;
                $result['bool'] = false;
                unset($this->userinfo['user_password']);
                $result['data'] = $this->userinfo;
                $result['msg'] = '用户名或密码不正确请重新输入！';
            }
        } else {
            $result = [];
            $result['code'] = 400;
            $result['bool'] = false;
            $result['data'] = ' ';
            $result['msg'] = $validate->getError();
        }
        return $result;
    }

    







    public function devicecheck($data){
       $result = Devicelog::check($data);
       $code = 1;
       switch ($result){
           case 1:
               $code = 204;
               break;
           case 2:
               $code = 405;
               break;
           case 3:
               $code = 406;
               break;
       }
       return $code;
    }
}