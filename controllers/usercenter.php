<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class usercenter extends MY_Controller {

    public $cancelReasons=[
                    [
                        'id'=>1,
                        'reason'=>'故障已经排除，下次再约！'
                    ],
                    [
                        'id'=>2,
                        'reason'=>'上门速度太慢，不想等了！'
                    ],
                    [
                        'id'=>3,
                        'reason'=>'没有修哥接单，太失望了！'
                    ]
        ];
    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();//调用http流方法
    }

    /**
     * get方法 获取用户信息
     * 
     */
    public function actionGet(){
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionGetUser"://登录获取用户信息
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['id'], $parames['token']);
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->UserInfoFunction($parames);
                }
                break;
            case "actionGetUserIcon"://登录获取用户头像昵称信息
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('id','用户Id','numeric|min_length[1]|max_length[11]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->UserIconFunction($parames);
                }
                break;
            case "actionGetCancelReasons"://取消订单原因
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
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
                    $outPut['data']=$this->cancelReasons;
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
    public function actionPost(){
        $parames=$this->parames;
            switch ($parames['action'])
            {
                case "actionUserConfirm"://用户实名认证
                        $apiVersion= isset($parames['versionCode']) ? $parames['versionCode'] : 0;//获取app版本
                        if($apiVersion<102){
                            $outPut=$this->confirmFunctionV1($parames);
                        }else{
                            $outPut=$this->confirmFunctionV2($parames);
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
     * [actionPut 用户中心put方法]
     * @return [type] [description]
     */
    public function actionPut(){
                $parames=$this->parames;
                switch ($parames['action'])
                {
                    case "actionUserUpdate"://用户编辑
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('province_id','省份id','trim|required');
                        $this->form_validation->set_rules('province_name','省份','trim|min_length[1]|required');
                        $this->form_validation->set_rules('city_id','市id','trim|min_length[1]|required');
                        $this->form_validation->set_rules('city_name','市','trim|min_length[1]|required');
                        $this->form_validation->set_rules('district_id','地区id','trim');
                        $this->form_validation->set_rules('district_name','地区','trim');
                        $this->form_validation->set_rules('address','详细地址','trim|min_length[1]|required');
                        $this->form_validation->set_rules('icon','用户头像url','trim|min_length[1]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['id'], $parames['token']);
                            $parames['icon']=json_decode($parames['icon'],true);//格式转换
                            $outPut=$this->updateUserFunction($parames);
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
     * [confirmFunctionV1 实名认证方法1.0.1]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function confirmFunctionV1($parames){
                       $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('name','姓名','trim|min_length[1]|max_length[16]|required');
                        $this->form_validation->set_rules('sfz','身份证号','trim|min_length[1]|max_length[18]|required');
                        $this->form_validation->set_rules('role_flag','角色Id','trim|min_length[1]|max_length[6]|required');
                        // $this->form_validation->set_rules('sfzphoto_z','身份证正面图片资源','trim|required');
                        // $this->form_validation->set_rules('sfzphoto_z_url','身份证正面图片url','trim|required');
                        // $this->form_validation->set_rules('sfzphoto_f','身份证反面图片url','trim|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['id'], $parames['token']);
                            // $this->confirmFunction($parames);//验证身份证
                            // $parames['sfzphoto_z']=json_decode($parames['sfzphoto_z_url'],true);//获取文件路径
                            // unset($parames['sfzphoto_z_url']);
                            // $parames['sfzphoto_f']=json_decode($parames['sfzphoto_f'],true);//获取文件路径
                            $outPut=$this->confirmUserFunctionv1($parames);//更新用户数据
                        }
                        return $outPut;
    }

    /**
     * [confirmFunctionV2 实名认证方法 1.0.2]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function confirmFunctionV2($parames)
    {
                       $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('name','姓名','trim|min_length[1]|max_length[16]|required');
                        $this->form_validation->set_rules('sfz','身份证号','trim|min_length[1]|max_length[18]|required');
                        $this->form_validation->set_rules('role_flag','角色Id','trim|min_length[1]|max_length[6]|required');
                        $this->form_validation->set_rules('province_id','省份id','trim|required');
                        $this->form_validation->set_rules('province_name','省份','trim|required');
                        $this->form_validation->set_rules('city_id','市id','trim|required');
                        $this->form_validation->set_rules('city_name','市','trim|required');
                        $this->form_validation->set_rules('district_id','地区id','trim');
                        $this->form_validation->set_rules('district_name','地区','trim');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['id'], $parames['token']);
                            $outPut=$this->confirmUserFunctionv2($parames);//更新用户数据
                        }
                        return $outPut;
    }


    /**
     * [confirmUserFunctionv1 实名认证方法(插入数据)   版本 1.0.1]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function confirmUserFunctionv1($parames)
    {
        //更新用户表
        $userData=[];
        $userData['realname_status']=1;
        $userData['id']=$parames['id'];
        $userData['name']=$parames['name'];
        $userData['sfz']=$parames['sfz'];
            //插入图片表数据
        // $imgData=[];
        // $imgData[0]['attr_id']=$parames['id'];//用户id
        // $imgData[0]['attr']=1;//用户
        // $imgData[0]['type']=2;//身份证正面
        // $imgData[0]['img_key']=$parames['sfzphoto_z']['key'];//图片key
        // $imgData[0]['img_hash']=$parames['sfzphoto_z']['hash'];
        // $imgData[0]['create_time']=time();
        // $imgData[0]['create_date']=date('Y-m-d,H:i:s',time());
        // $imgData[1]['attr_id']=$parames['id'];//用户id
        // $imgData[1]['attr']=1;//用户
        // $imgData[1]['type']=3;//身份证反面
        // $imgData[1]['img_key']=$parames['sfzphoto_f']['key'];
        // $imgData[1]['img_hash']=$parames['sfzphoto_f']['hash'];
        // $imgData[1]['create_time']=time();
        // $imgData[1]['create_date']=date('Y-m-d,H:i:s',time());
        M_Mysqli_Class('cro', 'UserModel')->trans_start();
        M_Mysqli_Class('cro', 'UserModel')->updateUser($userData);
        // M_Mysqli_Class('cro', 'ImgModel')->addImgPatch($imgData);
        M_Mysqli_Class('cro', 'UserModel')->trans_complete();
        $trans_status=M_Mysqli_Class('cro', 'UserModel')->trans_status();
        if ($trans_status) {
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="";
            $outPut['data']="";
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="认证失败";
            $outPut['data']="";
        }
        return $outPut;
    }


    /**
     * [confirmUserFunctionv2 实名认证插入数据方法 版本 1.0.2]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function confirmUserFunctionv2($parames)
    {
        //更新用户表
        $userData=[];
        $userData['realname_status']=1;
        $userData['id']=$parames['id'];
        $userData['name']=$parames['name'];
        $userData['sfz']=$parames['sfz'];
        $userData['province_id']=$parames['province_id'];
        $userData['province_name']=$parames['province_name'];
        $userData['city_id']=$parames['city_id'];
        $userData['city_name']=$parames['city_name'];
        $userData['district_id']=$parames['district_id'];
        $userData['district_name']=$parames['district_name'];
        M_Mysqli_Class('cro', 'UserModel')->trans_start();
        M_Mysqli_Class('cro', 'UserModel')->updateUser($userData);
        M_Mysqli_Class('cro', 'UserModel')->trans_complete();
        $trans_status=M_Mysqli_Class('cro', 'UserModel')->trans_status();
        if ($trans_status) {
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="";
            $outPut['data']="";
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="认证失败";
            $outPut['data']="";
        }
        return $outPut;
    }

    /**
     * [UserInfoFunction 获取单条用户信息方法]
     * @param [type] $parames [description]
     */
    public function UserInfoFunction($parames)
    {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        //查询图片表用户数据
        $userInfo = M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr($parames);
        $keyArray=array("id","role_flag","username","icon","name","sex","realname_status","sfz","province_id","province_name","city_id","city_name","district_id","district_name","address","area","status");
        $userInfo=$this->setArray($keyArray,$userInfo);
        $userInfo['icon']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$parames['id'],'attr'=>1,'type'=>1]);//用户头像
        // $userInfo['sfzphoto_z']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$parames['id'],'attr'=>1,'type'=>2]);
        // $userInfo['sfzphoto_f']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$parames['id'],'attr'=>1,'type'=>3]);
        // $userInfo['sfzphoto_z']='';
        // $userInfo['sfzphoto_f']='';
        return $userInfo;
    }

    /**
     * [UserIconFunction 根据id获取用户头像名称信息方法]
     * @param [type] $parames [description]
     */
    public function UserIconFunction($parames)
    {
        unset($parames['action']);
        //查询图片表用户数据
        $userInfo = M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr($parames);
        $keyArray=array("id","username","icon","name");
        $userInfo=$this->setArray($keyArray,$userInfo);
        $userInfo['icon']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$parames['id'],'attr'=>1,'type'=>1]);//用户头像
        return $userInfo;
    }
    /**
     * [updateUserFunction 更新用户信息]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function updateUserFunction($parames)
    {   
        //更新用户表
        $userData=[];
        $userData['id']=$parames['id'];
        $userData['province_id']=$parames['province_id'];
        $userData['province_name']=$parames['province_name'];
        $userData['city_id']=$parames['city_id'];
        $userData['city_name']=$parames['city_name'];
        $userData['district_id']=$parames['district_id'];
        $userData['district_name']=$parames['district_name'];
        $userData['address']=$parames['address'];
        //图片表数据
        $imgData=[];
        $imgData['attr_id']=$parames['id'];//用户id
        $imgData['attr']=1;//用户
        $imgData['type']=1;//touxiang
        $imgData['img_key']=$parames['icon']['key'];//图片key
        $imgData['img_hash']=$parames['icon']['hash'];
        //获取老数据
        $userInfo = M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr(['id'=>$parames['id']]);
        M_Mysqli_Class('cro', 'UserModel')->trans_start();
        M_Mysqli_Class('cro', 'UserModel')->updateUser($userData);
        if ($imgData['img_hash']!='') { //更新了cro的老图 则插入或更新数据,没更新头像则不插入图片表新数据
            $iconData=M_Mysqli_Class('cro', 'ImgModel')->getImgInfoByAttr(['attr_id'=>$parames['id'],'attr'=>1,'type'=>1]);//头像
            if (empty($iconData)) {//没有数据则插入
                $imgData['create_time']=time();
                $imgData['create_date']=date('Y-m-d,H:i:s',time());
                M_Mysqli_Class('cro', 'ImgModel')->addImg($imgData);
            }else{//有则更新
                $imgData['id']=$iconData['id'];
                $imgData['update_time']=time();
                $imgData['update_date']=date('Y-m-d,H:i:s',time());
                M_Mysqli_Class('cro', 'ImgModel')->updateImg($imgData);
            }
        }
        M_Mysqli_Class('cro', 'UserModel')->trans_complete();
        $trans_status=M_Mysqli_Class('cro', 'ImgModel')->trans_status();
        if ($trans_status) {
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="";
            $outPut['data']="";
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="网络问题,请重试";
            $outPut['data']="";
        }
        unset($userInfo);
        unset($userData);
        unset($imgData);
        return $outPut;
    }

    /**
     * [confirmFunction 身份认证方法]
     * 
     */
    // private function confirmFunction($parames)
    // {
    //         //获取文件信息
    //         list($type, $data) = explode(',', $parames['sfzphoto_z']);
    //         $res=$this->sfzVerify($data);
    //         if(!$res)
    //         {
    //              $outPut['status'] = 'error';
    //              $outPut['code']="0118";
    //              $outPut['msg'] = '验证失败,请重新验证';
    //              $outPut['data'] = '';
    //              $this->setOutPut($outPut);
    //         }
    //         //验证姓名和身份证号
    //         if ($parames['name']!=$res['name']) {
    //             $outPut['status'] = 'error';
    //             $outPut['code']="1200";
    //             $outPut['msg'] = '验证失败,身份证姓名不符';
    //             $outPut['data'] = '';
    //             $this->setOutPut($outPut);
    //         }
    //         if ($parames['sfz']!=$res['sfz']) {
    //             $outPut['status'] = 'error';
    //             $outPut['code']="1300";
    //             $outPut['msg'] = '验证失败,身份证号码不符';
    //             $outPut['data'] = '';
    //             $this->setOutPut($outPut);
    //         }
    //         return;
    // }

    /**
     * sfzVerify 身份验证接口
     * 
     */
    // protected function sfzVerify($data='')
    // {
    //         $host = "https://dm-51.data.aliyun.com";
    //         $path = "/rest/160601/ocr/ocr_idcard.json";
    //         $method = "POST";
    //         $appcode = "ad97e84018db44fabaf234991c86c72c";
    //         $headers = array();
    //         array_push($headers, "Authorization:APPCODE " . $appcode);
    //         //根据API的要求，定义相对应的Content-Type
    //         array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
    //         $querys = "";
    //         $bodys = '{
    //                               "inputs": [
    //                                 {
    //                                   "image": {
    //                                     "dataType": 50,
    //                                     "dataValue": "'.$data.'"
    //                                   },
    //                                   "configure": {
    //                                     "dataType": 50,
    //                                     "dataValue": "{\"side\":\"face\"}"
    //                                   }
    //                                 }
    //                               ]
    //                             }';
    //         $url = $host . $path;
    //         $curl = curl_init();
    //         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    //         curl_setopt($curl, CURLOPT_URL, $url);
    //         curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //         curl_setopt($curl, CURLOPT_FAILONERROR, false);
    //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//不直接输出
    //         curl_setopt($curl, CURLOPT_HEADER, 0);// 去掉头信息
    //         if (1 == strpos("$".$host, "https://"))
    //         {
    //             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    //             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    //         }
    //         curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
    //         $result=curl_exec($curl);
    //         //将json数据转化为array
    //         $sdata=json_decode($result,true);
    //         $ss=$sdata['outputs']['0']['outputValue']['dataValue'];
    //         $res=json_decode($ss,true);
    //         if ($res['success']==false) {
    //             return false;
    //         }
    //         if ($res['success']==true) {
    //             $ret['name']=$res['name'];
    //             $ret['sfz']=$res['num']; 
    //             return $ret;
    //         }
    // }
}
?>
