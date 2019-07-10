<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 * 订单申诉
 */
class orderappeal extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();//调用http流方法
    }



    /**
     * Post方法
     */
    public function actionPost(){
        $parames=$this->parames;
            switch ($parames['action'])
            {
                case "actionApplyAppeal"://提交申诉
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('claimant_id','申诉人Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('claimant_name','申诉人姓名','trim|min_length[1]|max_length[16]|required');
                        $this->form_validation->set_rules('claimant_phone','申诉人号码','trim|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('claimant_role_flag','申诉人角色','trim|min_length[1]|max_length[6]|required');
                        $this->form_validation->set_rules('order_id','申诉订单id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('appeal_type','申诉类型','trim|numeric|min_length[1]|required');
                        $this->form_validation->set_rules('appeal_content','申诉内容','trim|min_length[1]|max_length[255]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['claimant_id'], $parames['token']);
                            $outPut=$this->applyAppealFunction($parames);//
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
     * [applyAppealFunction 提交申诉]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function applyAppealFunction($parames)
    {   
        unset($parames['action']);
        unset($parames['token']);
        // $orderInfo = M_Mysqli_Class('cro','XiuOrdersModel')->getOrderInfoById($parames['order_id']);//订单信息
        $orderInfo = M_Mysqli_Class('cro','XiuOrdersModel')->getOrderFieldById(['id','order_status','fix_id','user_id'],$parames['order_id']);//订单信息
        //验证是否已发起申诉
        $check=M_Mysqli_Class('cro', 'OrderAppealModel')->checkAppealByorderid($parames['order_id']);
        if ($check>0) {
            $outPut['status']="error";
            $outPut['code']="1060";
            $outPut['msg']="该订单已提交申诉,请勿重复提交";
            $outPut['data']="";
            return $outPut;
        }
        if ($orderInfo['order_status']!=2) 
        {//判断订单状态,只有状态为修哥到达时才可以发起申诉
            $outPut['status']="error";
            $outPut['code']="3010";
            $outPut['msg']="申诉失败";
            $outPut['data']="";
            return $outPut;
        }
        $parames['platform_id']=2;
        M_Mysqli_Class('cro', 'XiuOrdersModel')->trans_start();
        M_Mysqli_Class('cro', 'OrderAppealModel')->addOrderAppeal($parames);
        M_Mysqli_Class('cro', 'XiuOrdersModel')->updateOrderAttr(['id'=>$parames['order_id'],'order_status'=>6]);
        M_Mysqli_Class('cro', 'XiuOrdersModel')->trans_complete();
        $trans_status=M_Mysqli_Class('cro', 'XiuOrdersModel')->trans_status();
        if ($trans_status) {
            $countentArr = array(
                        'type' => '1',
                        'data' => array("orderId"=>$orderInfo['id'],'order_status'=>'6')
                    );
            $countent = json_encode($countentArr);
            $pushReturn = F()->Jpush_module->send($orderInfo['fix_id'],[$orderInfo['user_id']],$countent,array('android'),'user');
            $data['pushReturn'] = $pushReturn;
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="提交成功,等待审核";
            $outPut['data']="";
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="网络问题,请重试";
            $outPut['data']="";
        }
        return $outPut;
    }


}
?>

