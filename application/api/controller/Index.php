<?php
namespace app\api\controller;

use app\api\module\indexModule;
use app\common\controller\Api;
use app\common\library\Sundry;
use app\common\module\Rule;
use Exception;
use think\Config;
use think\Db;
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
        $this->success('api连接成功',200,$config);
        return 0;
    }

    /**
     * 主页菜单
     */
    public function home()
    {
        $set = Rule::rule(Session::get('userinfo')['user_authId']);
        $this->ruleSet = indexModule::getMenuset($set);
        $this->success('查询成功!',201,$this->ruleSet);
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
     * @throws Exception
     */
    public function regular($find,$feature = null){
        switch ($find){
            case 'userinfo':
                if ($feature == null){
                    $this->success('响应成功',200,$this->jtw->getToken(['data'=>Session::get('user_info')]));
                }else{
                    if(request()->file('image')){
                        $res = indexModule::editUserinfo($feature,null);
                    }else{
                        $res = indexModule::editUserinfo($feature,input('data/a'));
                    }
                    $this->success($res,201);
                }
                break;
            default:
                $this->success('不存在的功能模块',201);
        }
    }

    /**
     * 系统管理
     * @param string $find 功能地址
     * @param null $feature 二级功能地址
     */
    public function system($find,$feature = null){
        $page = input('pagenum');
        $number = input('pagesize');
        $query = input('queryForm/a');
        if ($query != null) {
            $key = array_keys($query);
        }else{
            $key = [];
        }
        switch ($find){
            case 'admin':
                if($feature == null){
                    $sql = 'user_status != 4';
                    for($num = 0;$num<count($key);$num++){
                        if($key[$num] != 'user_addData'){
                            $sql = $sql.' and '.$key[$num].' like "%'.$query[$key[$num]].'%"';
                        }
                    }
                    $data = Db::table('sys_user')->alias('u')->join('sys_auth a','u.user_authId = a.id')->where($sql)->field('user_id,user_name,user_authId,user_phone,user_email,user_addData,user_status,au_name')->limit(($page-1)*10,$number)->select();
                    if($data != []){
                        $this->success('查询成功!',201,$data,Db::table('sys_user')->where('user_status != 4')->count());
                    }else{
                        $this->success('没有找到对应条件的用户!',201,[],count($data));
                    }
                }else{
                    $res = indexModule::admin($feature);
                    if($res['type'] == 'ok'){
                        if(array_key_exists('logdata',$res)){
                            $this->success($res['msg'],201,$res['data'],0,$res['logdata']);
                        }
                        $this->success($res['msg'],201,$res['data']);
                    }else{
                        $this->error($res['msg']);
                    }
                }
                break;
            case 'adminactionlog':
                $sql = '1 = 1';
                for($num = 0;$num<count($key);$num++){
                    if($key[$num] != 'user_addData'){
                        $sql = $sql.' and '.$key[$num].' like "%'.$query[$key[$num]].'%"';
                    }
                }
                $data = Db::table('sys_action')->alias('c')->join('sys_user u','c.user_id = u.user_id')->join('sys_menu m','c.menu_id = m.menu_id')->where($sql)->field('user_name,menu_name,ac_data,ac_uri,ac_editData')->limit(($page-1)*10,$number)->select();
                $this->success('管理员操作日志列表',201,$data,Db::table('sys_action')->count());
                break;
            case 'adminloginlog':
                $sql = '1 = 1';
                for($num = 0;$num<count($key);$num++){
                    if($key[$num] != 'user_addData'){
                        $sql = $sql.' and '.$key[$num].' like "%'.$query[$key[$num]].'%"';
                    }
                }
                $data = Db::table('sys_user_login')->alias('l')->join('sys_user u','l.user_id = u.user_id')->where($sql)->field('user_name,lg_status,lg_addData,lg_device,lg_ip')->limit(($page-1)*10,$number)->select();
                $this->success('管理员日志列表',201,$data,Db::table('sys_user_login')->count());
                break;
        }
    }
}
