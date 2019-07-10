<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class usertoken extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();
    }
    
    /**
     * get方法
     */
    public function actionGet(){
        $parames=$this->parames;
//         $this->load->library('common_rsa2');
//         $jsonParames=json_encode($parames);
//         echo $strSign = $this->common_rsa2->publicEncrypt($jsonParames);    //生成签名
//         echo $deSign = $this->common_rsa2->privateDecrypt($strSign); //验证签名
//         exit;
        switch ($parames['action'])
        {
            case "actionGetUserToken"://获取用户token
                unset($parames['action']);
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户ID','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('user_name','用户名','numeric|min_length[1]|max_length[15]|required');
                $this->form_validation->run();
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut=$this->getUserTokenFunction($parames);
                }
                $keyArray=array("token");
                $outPut['data']=$this->setArray($keyArray,$outPut['data']);
                break;
            default:
                $outPut['status']="error";
                $outPut['code']="4040";
                $outPut['msg']="请求错误";
                $outPut['data']="";
        }
        $this->setOutPut($outPut);
    }
    
    private function getUserTokenFunction($parames){
        $outPut['status']="ok";
        $outPut['code']="2000";
        $outPut['msg']="";
        $time=time();
        $row=M_Mysqli_Class('cro', 'UserTokenModel')->getTokenByAttr($parames['user_id'],$parames['user_name']);
        if(count($row)>0){
            if($row['over_time']>$time){
                $outPut['data']=$row;
            }else{
                $where['id']=$row['id'];
                $where['token']=md5($parames['user_id'].$parames['user_name'].$time);
                $where['create_time']=$time;
                $where['over_time']=$time+60*60*24;
                $actionUpdate=M_Mysqli_Class('cro', 'UserTokenModel')->updateUserToken($where);
                $where['user_name']=$parames['user_name'];
                $where['user_id']=$parames['user_id'];
                $outPut['data']=$where;
            }
        }else{
            $data['user_id']=$parames['user_id'];
            $data['user_name']=$parames['user_name'];
            $data['token']=md5($parames['user_id'].$parames['user_name'].$time);
            $data['create_time']=$time;
            $data['over_time']=$time+60*60*24;
            $actionUpdate=M_Mysqli_Class('cro', 'UserTokenModel')->addUserToken($data);
            $data['id']=$actionUpdate;
            $outPut['data']=$data;
        }
        return $outPut;
    }
    
   
    
    
   
    
 
}