<?php


namespace app\common\controller;
use app\common\library\Auth;
use app\common\module\Jwt;
use app\common\module\Loginlog;
use app\common\module\User;
use think\exception\HttpResponseException;
use think\Log;
use think\Request;
use think\Response;
use think\Session;


/**
 * API 控制父类
 */
class Api
{

    /**
     * @var Request 实例化
     */
    protected $request;

    /**
     *身份认证等操作
     * @var Auth 实例化
     */
    protected $auth;

    /**
     * JWT加密
     * @var Jwt 实例化
     */
    protected $jtw ;

    /**
     * 管理员表模型
     * @var User 实例化
     */
    protected $user;

    /**
     * 管理员登录日志模型
     * @var Loginlog 实例化
     */
    protected $loginlog;

    /**
     * API 构造方法
     * @param Request $request Request 对象
     */
    public function __construct(Request $request = null)
    {
        $this->request = is_null($request) ? Request::instance() : $request;
        $this->_initialize();
    }

    /**
     * API初始化
     */
    protected function _initialize(){
        if(Request::instance()->controller() && Request::instance()->action()!='login'){
            if (!Session::get('user_token')){
                $this->error('您还没有登录，请先登录!',500);
            }
            if(Session::get('user_token') != Request::instance()->header('token')){
//                Session::clear();
                $this->error('您已经在其它地方登录!',501);
            }
        }
        $this->auth = Auth::instance();
        $this->jtw = new Jwt();
        $this->user = new User();
        $this->loginlog = new Loginlog();
    }

    /**
     * 向前台发送消息
     * @param string $msg 提交的信息
     * @param int $code 返回状态
     * @param string $info 返回的用户信息
     * @param int $number 可选项
     */
    protected function result($msg,$code,$info = null,$number = 0)
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $info,
            'total'=> $number,
        ];
        if($number == 0){
            unset($result['total']);
        };
        $type = 'json';
        $response = Response::create($result, $type);
        throw new HttpResponseException($response);
    }

    /**
     * 获得成功处理后的数据
     * @param string $msg 返回的信息
     * @param int $code 返回的状态
     * @param string $info 返回的用户信息
     * @param int $number 可选
     */
    protected function success($msg = '',$code = 1,$info = '',$number = 0)
    {
        $this->result($msg,$code,$info,$number);
    }

    /**
     * 获得失败处理后的数据
     * @param string $msg 返回的信息
     * @param int $code 返回的状态
     */
    protected function error($msg,$code = 500){
        $this->result($msg,$code);
    }
}