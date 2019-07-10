<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 * 银行卡
 */
class bankcard extends MY_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->parames=$this->getParames();//调用http流方法
    }

    /**
     * get方法 获取信息
     * 
     */
    public function actionGet()
    {
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionGetBankcardList"://获取银行卡列表
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->BankcardListFunction($parames);
                }
                break;
            case "actionGetOneBankcard"://单银行卡信息
                    $this->form_validation->set_data($parames);
                    $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                    $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                    $this->form_validation->set_rules('bankcard_id','银行卡id','trim|min_length[1]|max_length[50]|required');
                    if ($this->form_validation->run() === FALSE) {
                        $outPut['status']="error";
                        $outPut['code']="1001";
                        $outPut['msg']=$this->form_validation->validation_error();
                        $outPut['data']="";
                    }else{
                        $this->_checkUserLogin($parames['user_id'], $parames['token']);
                        $outPut['status']="ok";
                        $outPut['code']="2000";
                        $outPut['msg']="";
                        $outPut['data']=$this->BankcardInfoFunction($parames['bankcard_id']);
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
     * Post方法
     */
    public function actionPost()
    {
        $parames=$this->parames;
            switch ($parames['action'])
            {
                case "actionBankcardAdd"://添加银行卡
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','用户id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('role_flag','用户角色','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('openbank','开户行','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('zhanghao','银行卡号','trim|min_length[1]|max_length[80]|required');
                        $this->form_validation->set_rules('name','名字','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('sfz','身份证号','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('phone','用户手机号','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $this->_checkBankcard($parames['zhanghao']);//验证该银行卡是否已经绑定
                            $this->_verifyBankcard($parames['zhanghao'],$parames['phone'],$parames['name'],$parames['sfz']);//验证银行卡信息
                            $outPut=$this->addBankcardFunction($parames);//添加新数据
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
     * [actionPut 银行卡put方法]
     * @return [type] [description]
     */
    public function actionPut()
    {
                $parames=$this->parames;
                switch ($parames['action'])
                {
                    case "actionBankcardUpdate"://银行卡编辑
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('user_id','用户id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('bankcard_id','银行卡id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('openbank','开户行','trim|min_length[1]|max_length[80]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $data=[];
                            $data['id']=$parames['bankcard_id'];
                            $data['openbank']=$parames['openbank'];
                            $outPut=$this->upBankcard($data);
                        }
                        break;
                    case "actionDeleteBankcard"://删除银行卡
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','用户id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('bankcard_id','银行卡id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $data=[];
                            $data['id']=$parames['bankcard_id'];
                            $data['status']=2;//删除状态
                            $outPut=$this->upBankcard($data);//删除银行卡
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
     * [actionDelete 银行卡delete方法]
     * @return [type] [description]
     */
    public function actionDelete()
    {
                $parames=$this->parames;
                switch ($parames['action'])
                {
                   
                    default:
                        $outPut['status']="error";
                        $outPut['code']="4040";
                        $outPut['msg']="请求错误";
                        $outPut['data']="";
                }
                $this->setOutPut($outPut);
    }

    /**
     * [upBankcard 更新银行卡]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function upBankcard($parames)
    {
        $res= M_Mysqli_Class('cro', 'BankCardModel')->updateBankcard($parames);
        if($res){
            $outPut['status'] = 'ok';
            $outPut['code']="2000";
            $outPut['msg'] = '编辑成功';
            $outPut['data'] = '';
        }else{
            $outPut['status'] = 'error';
            $outPut['code']="0118";
            $outPut['msg'] = '编辑失败';
            $outPut['data'] = '';
        }
        return $outPut;
    }

    /**
     * [addBankcardFunction 添加银行卡]
     * @param [type] $parames [description]
     */
    private function addBankcardFunction($parames)
    {
        //验证该卡之前有没有被删除,如果有被删除则直接将该卡的状态改为正常
        $oldBankCardId= M_Mysqli_Class('cro', 'BankCardModel')->checkUserBankcard($parames['user_id'],$parames['zhanghao'],2);
        if (!empty($oldBankCardId)) {
           $res= M_Mysqli_Class('cro', 'BankCardModel')->updateBankcard(['id'=>$oldBankCardId,'status'=>1]);
        }else{
            $data=[];
            $data['user_id']=$parames['user_id'];
            $data['openbank']=$parames['openbank'];
            $data['zhanghao']=$parames['zhanghao'];
            $data['role_type']=$parames['role_flag'];
            $data['name']=$parames['name'];
            $data['type']='bank';
            $res= M_Mysqli_Class('cro', 'BankCardModel')->addBankcard($data);
        }
        if($res){
            $outPut['status'] = 'ok';
            $outPut['code']="2000";
            $outPut['msg'] = '添加成功';
            $outPut['data'] = '';
        }else{
            $outPut['status'] = 'error';
            $outPut['code']="0118";
            $outPut['msg'] = '添加失败';
            $outPut['data'] = '';
        }
        return $outPut;
    }

    /**
     * [_verifyBankcard 验证银行卡]
     * @param  [type] $bank_num [description]
     * @param  [type] $phone    [description]
     * @param  [type] $name     [description]
     * @param  [type] $sfz      [description]
     * @return [type]           [description]
     */
    private function _verifyBankcard($bank_num,$phone,$name,$sfz)
    {
        //验证银行卡是否合法
        $url = "https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=".$bank_num."&cardBinCheck=true";
        $result = $this->curl($url);
        if (!$result['validated']) {
            $outPut['status'] = 'error';
            $outPut['code']="2004";
            $outPut['msg'] = '银行卡不合法';
            $outPut['data'] = '';
            $this->setOutPut($outPut);
        }
        if ($result['validated']=='1' && $result['cardType']=='CC') {
            $outPut['status'] = 'error';
            $outPut['code']="2005";
            $outPut['msg'] = '不支持信用卡';
            $outPut['data'] = '';
            $this->setOutPut($outPut);
        }
        //验证银行卡是否属于当前用户
        $host = "http://jisubank4.market.alicloudapi.com";
        $path = "/bankcardverify4/verify";
        $appcode = "ad97e84018db44fabaf234991c86c72c";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "bankcard=".$bank_num."&idcard=".$sfz."&mobile=".$phone."&realname=".urlencode($name);
        $url = $host . $path . "?" . $querys;
        $bank_result = $this->curl($url,'GET',$headers);
        if ($bank_result['status']>'0') {
            $outPut['status'] = 'error';
            $outPut['code']="2010";
            $outPut['msg'] = $bank_result['msg'];
            $outPut['data'] = '';
            $this->setOutPut($outPut);
        }
        if ($bank_result['result']['verifystatus']) {
            $outPut['status'] = 'error';
            $outPut['code']="2010";
            $outPut['msg'] = $bank_result['result']['verifymsg'];
            $outPut['data'] = '';
            $this->setOutPut($outPut);
        }
        return true;
    }

    /**
     * [_checkBankcard 验证该银行卡是否已经绑定]
     * @return [type] [description]
     */
    private function _checkBankcard($bank_num)
    {
        $row= M_Mysqli_Class('cro', 'BankCardModel')->checkBankcard($bank_num);
        if($row>0){
            $outPut['status'] = 'error';
            $outPut['code']="2006";
            $outPut['msg'] = '该银行卡已绑定';
            $outPut['data'] = '';
            $this->setOutPut($outPut);
        }else{
            return true;
        }
    }

    /**
     * [BankcardListFunction 获取银行卡列表]
     */
    private function BankcardListFunction($parames)
    {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        // $img_file='';//银行卡logo文件夹
        $parames['status']=1;
        $userInfo=M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr(['id'=>$parames['user_id']]);
        if (empty($userInfo)) {
           return;
        }
        $userIdList=M_Mysqli_Class('cro', 'UserModel')->getConditionUser(['username'=>$userInfo['username']]);
        $idlist=array_column($userIdList,'id');
        $res= M_Mysqli_Class('cro', 'BankCardModel')->getBankcardListByUids($idlist);
        $bank = include DEFAULT_SYSTEM_PATH.'libraries/bankname.php';
        foreach ($res as $key => $value) {
           $url = "https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=".$value['zhanghao']."&cardBinCheck=true";
           $curl_result = $this->curl($url);
           $res[$key]['des'] = $curl_result['validated'] ? $bank[$curl_result['bank']] : '';
           $res[$key]['img'] = $curl_result['validated'] ? $curl_result['bank'].'.png' : '';
           // $res[$key]['class'] = $curl_result['validated'] ? "ui-banklogo-s-".$curl_result['bank'] : '';
           $res[$key]['bank_num'] =$this->hidestr($value['zhanghao'],0,12);
        }
        $keyArray=array("id","bank_num","des","img");
        foreach ($res as $k => $v) 
        {
            $res[$k]=$this->setArray($keyArray,$v);
        }
        return $res;
    }

    /**
     * [BankcardInfoFunction 单银行卡信息]
     * @param [type] $parames [description]
     */
    private function BankcardInfoFunction($bankcard_id)
    {
        $data=[];
        $data['id']=$bankcard_id;
        $res=M_Mysqli_Class('cro', 'BankCardModel')->getBankcardByAttr($data);
        $keyArray=array("id","openbank","zhanghao","name");
        $res=$this->setArray($keyArray,$res);
        return $res;
    }

    /**
     * [hidestr 隐藏银行卡号]
     * @param  [type]  $string [description]
     * @param  integer $start  [description]
     * @param  integer $length [description]
     * @param  string  $re     [description]
     * @return [type]          [description]
     */
    public function hidestr($string, $start = 0, $length = 0, $re = '*') 
    {
        if (empty($string)) return false;
        $strarr = array();
        $mb_strlen = mb_strlen($string);
        while ($mb_strlen) {
            $strarr[] = mb_substr($string, 0, 1, 'utf8');
            $string = mb_substr($string, 1, $mb_strlen, 'utf8');
            $mb_strlen = mb_strlen($string);
        }
        $strlen = count($strarr);
        $begin  = $start >= 0 ? $start : ($strlen - abs($start));
        $end    = $last   = $strlen - 1;
        if ($length > 0) {
            $end  = $begin + $length - 1;
        } elseif ($length < 0) {
            $end -= abs($length);
        }
        for ($i=$begin; $i<=$end; $i++) {
            $strarr[$i] = $re;
        }
        if ($begin >= $end || $begin >= $last || $end > $last) return false;
        return implode('', $strarr);
    }

    protected function curl($url='',$method='GET',$headers=array()){
         $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_HEADER, false);//设置头文件的信息作为数据流输出
        if (1 == strpos("$".$url, "https://"))
        {
            //不验证证书和host
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }else{
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result,true);
    }
}
?>
