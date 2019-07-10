<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class register extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();
    }
    
    /**
     * post方法
     */
    public function actionPost(){
        $parames=$this->parames;
            switch ($parames['action'])
            {
                case "actionUserRegister"://用户注册
                    unset($parames['action']);
                    $this->form_validation->set_data($parames);
                    $this->form_validation->set_rules('username','手机号','trim|min_length[1]|max_length[11]|required');
                    $this->form_validation->set_rules('passwd','密码','trim|min_length[1]|max_length[16]|required');
                    $this->form_validation->set_rules('code','验证码','trim|min_length[1]|max_length[6]|required');
                    $this->form_validation->set_rules('role_flag','角色Id','trim|min_length[1]|max_length[6]|required');
                    $this->form_validation->run();
                    if ($this->form_validation->run() === FALSE) {
                        $outPut['status']="error";
                        $outPut['code']="1001";
                        $outPut['msg']=$this->form_validation->validation_error();
                        $outPut['data']="";
                    }else{
                        $outPut=$this->registerFunction($parames);
                    }
                    break;
                case 'actionRegisterJudge'://用户注册判断
                    $this->form_validation->set_rules('username','手机号','trim|min_length[1]|max_length[11]|required');
                    $this->form_validation->set_rules('role_flag','角色Id','trim|min_length[1]|max_length[6]|required');
                    if ($this->form_validation->run() === FALSE) {
                        $outPut['status']="error";
                        $outPut['code']="1001";
                        $outPut['msg']=$this->form_validation->validation_error();
                        $outPut['data']="";
                    }else{
                        $outPut=$this->RegisterJudgeFunction($parames);
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
     * 注册用户
     * @param unknown $parames
     */
    private function registerFunction($parames){
        $passWord=$parames['passwd'];
        $checkSmsCode=M_Mysqli_Class('cro', 'UserSmsModel')->checkUserSms($parames['username'],$parames['code'],2);
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
            $check = M_Mysqli_Class('cro', 'UserModel')->checkUserName($parames['username'],$parames['role_flag']);
            if($check>0){
                $this->load->library('encryption');
                $outPut['status']="error";
                $outPut['code']="1051";
                $outPut['msg']="该手机号已注册";
                $outPut['data']="";
            }else{
                $this->load->library('encryption');
                $key = bin2hex($this->encryption->create_key(16));
                $parames['passwd']=md5($key.$passWord);
                $parames['encryption_key']=$key;
                $parames['tel']=$parames['username'];
                $parames['createtime']=time();
                $parames['createip']=$this->getClientIP();
                $parames['status'] = $parames['role_flag']=='3' ? '0' : '1';
                unset($parames['dpasswd']);
                unset($parames['code']);
                $insertId = M_Mysqli_Class('cro', 'UserModel')->addUser($parames);
                if($insertId>0){
                    $deleteSmsCode = M_Mysqli_Class('cro', 'UserSmsModel')->deleteUserSms($checkSmsCode['id']);
                    if ($parames['role_flag']=='0') {//用户注册，赠送余额
                        $updateWallet = [
                                        'giving_balance'=>990,
                                        'total_balance'=>990
                                    ];
                        M_Mysqli_Class('cro','UserWalletModel')->actionWalletBalance($updateWallet,$insertId,'+');
                        $walletInfo = M_Mysqli_Class('cro', 'UserWalletModel')->getWalletByUserId($insertId);//获取钱包信息
                        $WalletLogData = $this->WalletLogData($insertId,$walletInfo['id'],990,$walletInfo['balance'],$walletInfo['balance'],'1','7',0,$walletInfo['giving_balance']-990,$walletInfo['giving_balance']);
                        M_Mysqli_Class('cro', 'UserWalletLogModel')->addUserWalletLog($WalletLogData);//新增钱包日志
                    }
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="注册成功";
                    $outPut['data']=array("user_id"=>$insertId);
                }else{
                    $outPut['status']="error";
                    $outPut['code']="0118";
                    $outPut['msg']="网络问题";
                    $outPut['data']="";
                }
            }
        }
        return $outPut;
    }
    //钱包日志数组
    private function WalletLogData($user_id,$wallet_id,$amount,$before_balance='0',$after_balance,$income_type,$type,$primary_id,$before_giving_balance='0',$after_giving_balance){
        $data = [
                        'user_id' => $user_id,
                        'wallet_id' => $wallet_id,
                        'amount' => $amount,
                        'before_balance' => $before_balance,
                        'after_balance' => $after_balance,
                        'before_giving_balance'=>$before_giving_balance,
                        'after_giving_balance' =>$after_giving_balance,
                        'income_type' => $income_type,
                        'type' => $type,
                        'primary_id' =>$primary_id
                    ];
        return $data;
    }
    /**
     * 发送验证短信
     */
    public function sendCheckSms(){
        if (!$this->_checkAjax()) {
            return;
        }
        $this->form_validation->setRequestMethod('post');
        $this->form_validation->set_rules('mobile_phone', '', 'required|less_than[12]');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        $codeSesstion=$this->session->tempdata('mobile_phone');
        if($this->checkMolieRules($data['mobile_phone'])===false){
            echo 'mobileError';exit;
        }
        if(time()<$codeSesstion['time']+60){
            echo "frequency";exit;
        }
        else{
            $code['code']=$this->randStr(6,'NUMBER');
            $code['time']=time();
            $content="欢迎注册柯力士安犬平台，您当前的注册验证码".$code['code']."，请在10分钟内使用。";
            $content=$this->utf82gbk($content);
            $url = "http://sms.aqdog.com/Sms_module.php";
            $postData = array(
                    'mobile' => $data['mobile_phone'],
                    'content' => $content
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            $output = curl_exec($ch);
            curl_close($ch);
            $return = json_decode($output, true);
            //$return=F()->Sms_module->sendSms($data['mobile_phone'],$content);
            //$return['result']=0;
            if($return['result']==0){
                $this->session->set_tempdata('moblie', $code, 600);
                //$this->session->set_tempdata($data['mobile_phone'], $code,"300");
                echo "sendSuccess";
            }
            else{
                echo "sendFailure";
            }
        }
    } 

    /**
     * 判断手机号是否注册
     */
    private function RegisterJudgeFunction($parames){
        $check = M_Mysqli_Class('cro', 'UserModel')->checkUserName($parames['username'],$parames['role_flag']);
            if($check>0){
                $outPut['status']="error";
                $outPut['code']="1051";
                $outPut['msg']="该手机号已注册";
                $outPut['data']="";
            }else{
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="该手机号未注册";
                $outPut['data']="";
            }
            return $outPut;
    }
}
?>