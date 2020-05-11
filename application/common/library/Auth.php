<?php


namespace app\common\library;


use app\common\module\Loginlog;
use app\common\module\User;
use app\common\module\Encryption;
use think\Config;
use think\Log;
use think\Request;
use think\Validate;

class Auth
{

    /**
     * AUTH初始化
     * @var null
     */
    protected static $instance = null;

    /**
     * API配置信息
     * @var array
     */
    protected $config = [];

    /**
     * 用户名暂存
     * @var string
     */
    protected $username = '';

    /**
     * 用户盐值信息
     * @var string
     */
    protected $field = '';

    /**
     * 用户信息暂存
     * @var array
     */
    protected $userinfo = [];

    /**
     * Auth 构造方法
     * @param array $options
     */
    public function __construct($options = [])
    {
        if ($config = Config::get('user')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->options = array_merge($this->config, $options);
    }

    /**
     * 初始化
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
     * 用户登录
     * @param $username
     * @param $password
     * @return array
     * @throws \think\exception\DbException
     */
    public function login($username, $password)
    {
        //验证器规则
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
        //实例化验证器
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
            //密码加密判断
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
                        $result['msg'] = '第一次登录，请先完善个人信息!';
                        break;
                    case 4:
                        $result['code'] = 500;
                        $result['msg'] = '用户名或密码不正确请重新输入!';
                        break;
                }

            } else {
                $result = [];
                $result['code'] = 401;
                $result['bool'] = false;
                unset($this->userinfo['user_password']);
                $result['data'] = [];
                $result['msg'] = '用户名或密码不正确请重新输入！';
            }
        } else {
            $result = [];
            $result['code'] = 400;
            $result['bool'] = false;
            $result['data'] = null;
            $result['msg'] = $validate->getError();
        }
        if($result['code']==201||$result['code']==203){
            Loginlog::loginlog(['user_id'=>$result['data']['user_id'],
                'lg_ip'=>Request::instance()->ip(),'lg_status'=>$result['code'],
                'lg_addData'=>time(),
                'lg_device'=>'ceshishebei']);
        }
        return $result;
    }

//    public function devicecheck($data){
//       $result = Devicelog::check($data);
//       $code = 1;
//       switch ($result){
//           case 1:
//               $code = 204;
//               break;
//           case 2:
//               $code = 405;
//               break;
//           case 3:
//               $code = 406;
//               break;
//       }
//       return $code;
//    }
}