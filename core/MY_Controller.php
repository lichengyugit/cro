<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class MY_Controller extends CI_Controller {
    //修铺代理商信息数组
     protected $xiubikeAgent = array(
                                                            'id' => 1000,
                                                            'user_id'=>28,
                                                            'name'=>'快修自营',
                                                     );
     protected $xiubikeAgentId=1;//修铺自营代理商表id
     protected $commission = array('fix'=>600, 'agent'=>290);
     protected $activateCommission =  array('fix'=>2900, 'agent'=>0);//保险激活分佣
     protected $shopCommission = 9900;
     protected $fixStstusTime=30;//修哥操作开工状态时间
    // public $userinfo = FALSE;
    // protected $langs;
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * [_checkUserLogin 验证是否登录]
     * @param  [type] $user_id [description]
     * @param  [type] $token   [description]
     * @return [type]          [description]
     */
    public function _checkUserLogin($user_id,$token) {
        $row=M_Mysqli_Class('cro', 'UserTokenModel')->checkToken($user_id,$token);
        $time=time();
        if(count($row)>0){
            if($time>$row['over_time']){
                $outPut['status'] = 'error';
                $outPut['code'] = '0112';
                $outPut['msg'] = '登录超时';
                $outPut['data'] = '';
                $this->setOutPut($outPut);
            }else{
                return;
            }
        }else{
            $outPut['status'] = 'error';
            $outPut['code'] = '0110';
            $outPut['msg'] = '未登录';
            $outPut['data'] = '';
            $this->setOutPut($outPut);
        }
        
    }
    
    /**
     * [setOutPut 输出内容]
     * @param [json] $outPut [description]
     */
    public function setOutPut($outPut){
        echo json_encode($outPut);exit;
    }

    /**
     * [setArray 过滤数据]
     * @param [type] $keyArray [description]
     * @param [type] $data     [description]
     */
    public function setArray($keyArray,$data){
      if(count($keyArray)>0){
        foreach($keyArray as $k=>$v){
            $setData[$v]=$data[$v];
        }
        return $setData;
      }
        return $data;
    }
    
    public function getClientIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }
    
    /**
     * 短信发送
     */
    public function sendSms($mobile,$code_type,$smsId){
        $code=rand(100000,999999);
        if($code_type==1){//登录
            $decribe = '验证码:'.$code.'，欢迎登录修铺（为保证帐号安全，请勿向他人透露）';
        }elseif($code_type==2){//注册
            $decribe = '验证码:'.$code.'，欢迎注册修铺（为保证帐号安全，请勿向他人透露）';
        }elseif($code_type==3){//找回密码
            $decribe = '验证码:'.$code.'，您正在找回密码，请确保本人使用（为保证帐号安全，请勿向他人透露）';
        }
        $this->load->library('Sms/ChuanglanSmsApi');
        $result = $this->chuanglansmsapi->sendSMS($mobile,$decribe);
        if(!is_null(json_decode($result))){
            $output = json_decode($result,true);
            if(isset($output['code'])  && $output['code']=='0'){
                $data['mobile']=$mobile;
                $data['code_type']=$code_type;
                $data['code']=$code;
                $time=time();
                $data['over_time']=$time+60*15;
                if($smsId==0){
                   $addSms=M_Mysqli_Class('cro', 'UserSmsModel')->addSms($data);
                }else{
                   $data['id']=$smsId;
                   $updSms=M_Mysqli_Class('cro', 'UserSmsModel')->updateSms($data);
                   unset($data['id']);
                }
                $data['send_time']=$time;
                $data['sms_context']=json_encode(array("send"=>$decribe,"return"=>$output));
                unset($data['over_time']);
                $addSmsLog=M_Mysqli_Class('cro', 'SmsLogModel')->addSmsLog($data);
                $outPut['status']="ok";
                $outPut['msg']="发送成功";
                $outPut['data']="";
            }else{
                $outPut['status']="error";
                $outPut['msg']="发送失败";
                $outPut['data']=$output['errorMsg'];
            }
        }else{
                $outPut['status']="error";
                $outPut['msg']="发送失败";
                $outPut['data']=$output['errorMsg'];
        }
        return $outPut;
    }
    
    /**
     * 生成订单号
     */
    public function createOrderNum($data,$head){
        if($head=="XO"){
            $str=$this->getOxOrderLocationNum($data);
            return $head.date("YmdH",time()).$str.substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        }
        
    }
    
    /**
     * 地区组成
     */
    private function getOxOrderLocationNum($data){
        $str="";
        if(count($data['province_id'])<=2){
            $str.="0".$data['province_id'];
        }else{
            $str.=$data['province_id'];
        }
        if(count($data['city_id'])<=3){
            $str.="0".$data['city_id'];
        }else{
            $str.=$data['city_id'];
        }
        if(count($data['district_id'])<=4){
            $str.="0".$data['district_id'];
        }else{
            $str.=$data['district_id'];
        }
        return $str;
    }
    
    /**
     * 处理http流参数
     */
    private function actionHttpStreaming($streaming){
        $arr=explode("&", $streaming);
        foreach($arr as $k=>$v){
            $keyArray=explode("=", $v);
            $return[$keyArray[0]]=$keyArray[1];
        }
        return $return;
    }
    
    /**
     * 根据请求处理参数
     * @return unknown
     */
    public function getParames(){
        $method=$_SERVER['REQUEST_METHOD'];
        if($method=="GET"){
            return $this->input->get();
        }else{
            if(ISSIGN==FALSE){
                return $this->input->input_stream();
            }else{
                $streaming = $this->input->input_stream();
                //$uriArray=$this->actionHttpStreaming($streaming);
                $this->load->library('common_rsa2');
                $deSign = $this->common_rsa2->privateDecrypt($streaming['sign']); //验证签名
    //              print_r($streaming);
    //              print_r($this->actionHttpStreaming(urldecode($deSign)));exit;
                return $this->actionHttpStreaming(urldecode($deSign));
            }
        }
        
    }

    /**
     * [create_dir 创建文件夹]
     * @param  [type]  $dirName   [路径名]
     * @param  integer $recursive [description]
     * @param  integer $mode      [权限为777]
     * @return [type]             [description]
     */
    public function create_dir($dirName, $recursive = 1,$mode=0777) {
        ! is_dir ( $dirName ) && mkdir ( $dirName,$mode,$recursive );
    }

    /**
     * [_cut 截取字符串]
     * @param  [type] $begin [description]
     * @param  [type] $end   [description]
     * @param  [type] $str   [description]
     * @return [type]        [description]
     */
     public function _cut($begin,$end,$str)
     {
        $b = mb_strpos($str,$begin) + mb_strlen($begin);
        $e = mb_strpos($str,$end) - $b;
        return mb_substr($str,$b,$e);
    }



}