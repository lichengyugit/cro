<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class feedback extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();
    }

    /**
     * Post方法
     */
    public function actionPost()
    {
        $parames=$this->parames;
            switch ($parames['action'])
            {
                case "actionAddFeedback"://反馈
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('name','姓名','trim|min_length[1]|required');
                        $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('content','信息','trim|min_length[1]|required');
                        $this->form_validation->set_rules('tel','电话','trim|min_length[1]|required');
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $outPut = $this->addFeedbackFunction($parames);//提交反馈
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
     * [addFeedbackFunction 提交反馈]
     * @param [type] $parames [description]
     */
    private function addFeedbackFunction($parames)
    {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        $parames['platform_id']=2;
        $parames['create_time']=time();
        $parames['create_date']=date('Y-m-d,H:i:s',time());
        $res=M_Mysqli_Class('cro', 'FeedbackModel')->addFeedback($parames);
        if($res>0){
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="反馈成功";
            $outPut['data']="";
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="反馈失败";
            $outPut['data']="";
        }
        return $outPut;
    }
}