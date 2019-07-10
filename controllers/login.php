<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class login extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();
    }
    
    /**
     * post方法
     */
    public function actionPost(){
        //$a=$this->input->input_stream();
        //print_r($a);
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionUserLogin"://登录获取用户信息
                //print_r();
                //$this->form_validation->setRequestMethod('');
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('username','手机号','trim|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('passwd','密码','trim|min_length[1]|max_length[16]|required');
                $this->form_validation->set_rules('role_flag','角色Id','trim|min_length[1]|max_length[6]|required');
                $this->form_validation->run();
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut=$this->loginFunction($parames);
                }
                break;
            case "actionSendSms"://发送短信
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('mobile','手机号','trim|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('code_type','短信类型','trim|min_length[1]|max_length[16]|required');
                $this->form_validation->run();
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut=$this->sendSmsFunction($parames);
                }
                break;
            case "actionForgetPassword"://忘记密码
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('mobile','手机号','trim|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('code','验证码','trim|min_length[1]|max_length[16]|required');
                $this->form_validation->set_rules('passwd','密码','trim|min_length[1]|max_length[16]|required');
                $this->form_validation->set_rules('role_flag','用户角色','trim|min_length[1]|max_length[16]|required');
                $this->form_validation->run();
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut=$this->forgetPasswordFunction($parames);
                }
                break;
            case "actionUpdatePassword"://修改密码
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户ID','trim|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('old_passwd','旧密码','trim|min_length[1]|max_length[20]|required');
                $this->form_validation->set_rules('passwd','密码','trim|min_length[1]|max_length[20]|required');
                $this->form_validation->run();
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $outPut=$this->updatePasswordFunction($parames);
                }
                break;
            default:
                $outPut['status']="error";
                $outPut['code']="4040";
                $outPut['msg']="请求错误";
                $outPut['data']="";
        }
        $this->setOutPut($outPut);
    }
    

    /**
     * get方法
     */
    public function actionGet(){
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionGetUser"://登录获取用户信息
                echo 1;exit;
                break;
            default:
                $outPut['status']="error";
                $outPut['code']="4040";
                $outPut['msg']="请求错误";
                $outPut['data']="";
        }
        $this->setOutPut($outPut);
    }
    
    
    /**
     * 登录方法
     */
    private function loginFunction($parames){
        unset($parames['action']);
        $passWord=$parames['passwd'];
        unset($parames['passwd']);
        $userInfo = M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr($parames);
        $passWord=md5($userInfo['encryption_key'].$passWord);
        $checkUser = M_Mysqli_Class('cro', 'UserModel')->checkUserByNameAndPass($userInfo['username'],$passWord,$parames['role_flag']);
        if(count($userInfo)==0){
            $outPut['status']="error";
            $outPut['code']="1050";
            $outPut['msg']="用户名不存在";
            $outPut['data']="";
        }elseif($checkUser==0){
            $outPut['status']="error";
            $outPut['code']="1060";
            $outPut['msg']="密码错误";
            $outPut['data']="";
        }else{
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="登录成功";
            $keyArray=array("id","role_flag","username","icon","name","sex","realname_status","sfz","address","area","status");
            $userInfo=$this->setArray($keyArray,$userInfo);
            $userInfo['icon']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$userInfo['id'],'attr'=>1,'type'=>1]);//用户头像
            // $userInfo['sfzphoto_z']='';
            // $userInfo['sfzphoto_f']='';
            // $userInfo['sfzphoto_z']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$userInfo['id'],'attr'=>1,'type'=>2]);
            // $userInfo['sfzphoto_f']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$userInfo['id'],'attr'=>1,'type'=>3]);
            // $userInfo['shop_apply_status']='';//店铺入驻状态
            // $userInfo['shop_id']='';//店铺id
            // if ($userInfo['role_flag']==1) 
            // {//用户为商家角色时 判断是否入驻
            //    $shopInfo= M_Mysqli_Class('cro', 'ShopModel')->getShopInfoByAttr(['user_id'=>$userInfo['id']]);
            //    if (empty($shopInfo))
            //     {
            //        $userInfo['shop_apply_status']='-1';//未入住状态
            //    }else{
            //        $userInfo['shop_apply_status']=$shopInfo['status'];//已入驻状态
            //        $userInfo['shop_id']=$shopInfo['id'];//已入驻状态
            //    }
            // }
            $userInfo['agent_apply_status']='';//店铺入驻状态
            $userInfo['agent_id']='';//代理商id
            if ($userInfo['role_flag']==4) 
            {
                //用户为代理商角色时 判断是否入驻
               $agentInfo= M_Mysqli_Class('cro', 'XiuAgentModel')->getAgentInfoByAttr(['user_id'=>$userInfo['id']]);
               $userInfo['agent_name']=$agentInfo['name'];
               if (empty($agentInfo))
                {
                   $userInfo['agent_apply_status']='-1';//未入住状态
               }else{
                   $userInfo['agent_apply_status']=$agentInfo['status'];//已入驻状态
                   $userInfo['agent_id']=$agentInfo['id'];//已入驻状态
               }
            }
            $outPut['data']=$userInfo;
        }
        return $outPut;
    }


    /**
     * 发送短信
     */
    private function sendSmsFunction($parames){
        unset($parames['action']);
        $getSms=M_Mysqli_Class('cro', 'UserSmsModel')->getUserSms($parames['mobile'],$parames['code_type']);
        if($getSms['c']=="0"){
            $outPut=$this->sendSms($parames['mobile'],$parames['code_type'],0);
        }elseif($getSms['c']>0 && $getSms['timeOut']=="out"){
            $outPut=$this->sendSms($parames['mobile'],$parames['code_type'],$getSms['id']);
        }elseif($getSms['c']>0 && $getSms['timeOut']=="over"){
            $outPut=$this->sendSms($parames['mobile'],$parames['code_type'],$getSms['id']);
        }else{
            $outPut['status']="error";
            $outPut['code']="1070";
            $outPut['msg']="不可重复获取短信";
            $outPut['data']="";
        }
        return $outPut;
    }
    
    /**
     * 修改密码
     */
    private function updatePasswordFunction($parames){
        unset($parames['action']);
        $userInfo = M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr(array("id"=>$parames['user_id']));
        $passwd=$userInfo['passwd'];
        $oldpasswd=md5($userInfo['encryption_key'].$parames['old_passwd']);
        if($passwd==$oldpasswd){
            $parames['passwd']=md5($userInfo['encryption_key'].$parames['passwd']);
            $parames['id']=$parames['user_id'];
            unset($parames['token']);
            unset($parames['old_passwd']);
            $user_id=$parames['user_id'];
            unset($parames['user_id']);
            $updPass = M_Mysqli_Class('cro', 'UserModel')->updateUser($parames);
            if($updPass>0){
                $del = M_Mysqli_Class('cro', 'UserTokenModel')->deleteByParam(array("user_id"=>$user_id));
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="修改成功，请重新登录";
                $outPut['data']="";
            }
        }else{
            $outPut['status']="error";
            $outPut['code']="1060";
            $outPut['msg']="密码错误，请重新输入";
            $outPut['data']="";
        }
        return $outPut;
    }
    
    /**
     * 忘记密码方法
     */
    private function forgetPasswordFunction($parames){
        unset($parames['action']);  
        unset($parames['token']);
        $passWord=$parames['passwd'];
        $checkSmsCode=M_Mysqli_Class('cro', 'UserSmsModel')->checkUserSms($parames['mobile'],$parames['code'],3);
        if($checkSmsCode['c']==0){
            $outPut['status']="error";
            $outPut['code']="1080";
            $outPut['msg']="验证码错误";
            $outPut['data']="";
        }elseif($checkSmsCode['timeOut']=="out"){
            $outPut['status']="error";
            $outPut['code']="1090";
            $outPut['msg']="验证码已过期";
            $outPut['data']="";
        }else{
            $userInfo = M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr(array("username"=>$parames['mobile'],"role_flag"=>$parames['role_flag']));
            $parames['passwd']=md5($userInfo['encryption_key'].$parames['passwd']);
            $parames['id']=$userInfo['id'];
            unset($parames['code']);
            unset($parames['mobile']);
            unset($parames['role_flag']);
            $updPass = M_Mysqli_Class('cro', 'UserModel')->updateUser($parames);
            if($updPass>0){
                $deleteSmsCode = M_Mysqli_Class('cro', 'UserSmsModel')->deleteUserSms($checkSmsCode['id']);
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="修改成功";
                $outPut['data']="";
            }else{
                $outPut['status']="error";
                $outPut['code']="0118";
                $outPut['msg']="网络问题";
                $outPut['data']="";
            }
        }
        return $outPut;
    }
       
    
}
?>
