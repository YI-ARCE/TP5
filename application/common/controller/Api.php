<?php


namespace app\common\controller;
use app\common\library\Auth;
use app\common\module\Jwt;
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
     * @var Auth 实例化
     */
    protected $auth;
    /**
     * @var Jwt 实例化
     */
    protected $jtw ;
//    /**
//     * @var autologin 判断是否可以自动登录
//     */
//    protected $autologin;

    /**
     * Api 构造方法
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
                $this->error('您还没有登录，请先登录!',[],500);
            }
            if(Session::get('user_token') != input('token')){
                Log::log(Session::get('user_token'));
                Log::log(input('user_token'));
                Session::clear();
                $this->error('您已经在其它地方登录!',[],501);
            }
        }
//        $this->autologin =1;
        $this->auth = Auth::instance();
        $this->jtw = new Jwt();
    }

    /**
     * @param string $msg 提交的信息
     * @param array $data 返回的数据
     * @param int $code 返回状态
     * @param string $info 返回的用户信息
     */

    protected function result($msg,$data,$code,$info = null)
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'token' => $data,
            'data' => $info
        ];
        $type = 'json';
        $response = Response::create($result, $type);
        throw new HttpResponseException($response);
    }

    /**
     * @param string $msg 返回的信息
     * @param mixed $data 返回的数据
     * @param int $code 返回的状态
     * @param string $info 返回的用户信息
     */

    protected function success($msg = '', $data = null, $code=1,$info = '')
    {
        $this->result($msg,$data,$code,$info);
    }
    /**
     * @param string $msg 返回的信息
     * @param mixed $data 返回的数据
     * @param int $code 返回的状态
     */
    protected function error($msg,$data,$code){
        $this->result($msg,$data,$code);
    }
}