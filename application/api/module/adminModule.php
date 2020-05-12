<?php


namespace app\api\module;


use app\common\library\Sundry;
use app\common\module\Encryption;
use app\common\module\User;
use think\Config;
use think\Log;
use think\Validate;

class adminModule
{
    /**添加管理员
     * @param array $data 处理的数据
     * @return array
     */
    public static function addUser($data){
        $savedata = Config::get('user');
        $result = new User();
        if (count($result->where('user_name',$data['user_name'])->column('user_name'))==1){
            return ['msg'=>'已存在的用户名!','data'=>null,'type'=>'error'];
        }
        $rule = [
            'user_name' => 'require|alphaDash|length:6,11',
            'user_password' => 'require|length:6,18'
        ];
        $message = [
            'user_name' => '用户名或密码格式填写不正确',
            'user_password' => '用户名或密码格式填写不正确',
        ];
        $valdata = [
            'user_name' => $data['user_name'],
            'user_password' => $data['user_password'],
        ];
        $validate = new Validate($rule, $message);
        $val = $validate->check($valdata);
        if ($val){
            $data['user_salt'] = Sundry::random(6);
            $data['user_password'] = Encryption::enc($data['user_password'],$data['user_salt']);
            foreach ($savedata as $key => $val){
                if(array_key_exists($key,$data)){
                    $savedata[$key] = $data[$key];
                }
            }
            Log::log($result->save($savedata));
            return ['msg'=>'添加成功','data'=>null,'type'=>'ok','logdata'=>'添加用户:'.$data['user_name']];
        }else{
            return ['msg'=>$validate->getError(),'data'=>null,'type'=>'ok'];
        }
    }

    /**
     * 修改管理员信息
     * @param array $data 处理的数据
     * @return array
     */
    public static function editUser($data){
        $savedata = Config::get('user');
        $result = new User();
        $logdata = "";
        foreach ($savedata as $key => $val){
            if(array_key_exists($key,$data)){
                $savedata[$key] = $data[$key];
                $logdata = $logdata.' '.$key.':'.$data[$key].' ';
            }else{
                unset($savedata[$key]);
            }
        }
        Log::log($logdata);
        $result->where('user_id',$data['user_id'])->update($savedata);
        return ['msg'=>'修改成功','data'=>null,'type'=>'ok','logdata'=>$logdata];
    }

    /**
     * 删除管理员
     * @param array $data 处理的数据
     * @return array
     */
    public static function deleteUser($data){
        $result = new User();
        $result->where('user_id',$data['user_id'])->update(['user_status'=>4]);
        return ['msg'=>'用户已经删除！','data'=>null,'type'=>'ok','logdata'=>$data['user_name']];
    }

    /**
     * 查找管理员
     * @param array $data 处理的数据
     * @return array
     */
    public static function selectUser($data){
        $result = new User();
        if(array_key_exists('user_id',$data)){
            $result->where('user_id',$data['user_id'])->where('user_status !=4');
        }else{
            $result->where('user_name',$data['user_name'])->where('user_status !=4');
        }
        $data = $result->column(true);
        if(count($data) == 0){
            return ['msg'=>'未能找到此用户','data'=>null,'type'=>'ok'];
        }
        $data = array_values($data);
        $data = $data[0];
        unset($data['user_password'],$data['user_salt'],$data['user_token'],$data['user_realname'],$data['user_number'],
            $data['user_sex'],$data['user_addData'],$data['user_head']);
        return ['msg'=>'查询成功','data'=>$data,'type'=>'ok'];
    }

    /**
     * 更改管理员状态
     * @param array $data 处理的数据
     * @return array
     */
    public static function changeStatus($data){
        $result = new User();
        $result->where('user_id',$data['user_id'])->update(['user_status'=>$data['user_status']]);
        if($data['user_status'] == 1){
            return ['msg'=>'用户已启用！','data'=>null,'type'=>'ok','logdata'=>'enable:'.$data['user_name']];
        }else{
            return ['msg'=>'用户已禁用！','data'=>null,'type'=>'ok','logdata'=>'disable'.$data['user_name']];
        }
    }
}