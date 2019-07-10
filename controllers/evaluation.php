<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class evaluation extends MY_Controller {

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
//         $strSign=json_encode($parames);
//         echo $deSign = $this->common_rsa2->privateDecrypt($strSign); //验证签名
//         exit;
        switch ($parames['action'])
        {
            case "actionGetStarTag"://获取订单评价时各星级tag
                unset($parames['action']);
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('star','星级','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('type','评价类型','numeric|min_length[1]|max_length[11]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->getStarTagFunction($parames);
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
     * post方法
     */
    public function actionPost(){
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionOrderEvaluation"://发布评价
                //print_r($parames);exit;
                unset($parames['action']);
                //$this->_checkUserLogin(3070, "5da3c3840947c121416fb760749890b0");
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('platform_id','平台Id','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('technology_star','技术星级','numeric|max_length[1]|required');
                $this->form_validation->set_rules('service_star','服务星级','numeric|max_length[1]|required');
                $this->form_validation->set_rules('speed_star','速度星级','numeric|max_length[1]|required');
                $this->form_validation->set_rules('order_id','订单Id','numeric|min_length[1]|max_length[5]|required');
                $this->form_validation->set_rules('content','用户评价内容','trim|min_length[1]|max_length[255]');
                $this->form_validation->set_rules('tag','用户选择tag','trim');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $outPut=$this->OrderEvaluationFunction($parames);
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
     * put方法
     */
    public function actionPut(){
        $parames=$this->parames;
    }
    
    /**
     * put方法
     */
    public function actionDelete(){
        $parames=$this->parames;
    }
    
    /**
     * 根据条件获得星级tag列表方法
     * @param unknown $parames
     */
    private function getStarTagFunction($parames){
       $list=M_Mysqli_Class('cro', 'StarTagModel')->getStarTagByAttr($parames);
       $keyArray=array("id","tag_name","star","type");
       foreach($list as $k=>$v){
          $data[]=$this->setArray($keyArray,$v);
       }
       return $data;
   }
    
   /**
    * 订单评价
    */ 
   private function OrderEvaluationFunction($parames){
       unset($parames['token']);
       $outPut['status']="error";
       $outPut['code']="1001";
       $outPut['msg']="非法提交，参数有误。";
       $outPut['data']="";
       $checkOrderEvaluation=M_Mysqli_Class('cro', 'OrderEvaluationModel')->checkOrderEvaluation($parames);
       //echo $checkOrderEvaluation;exit;
       if($checkOrderEvaluation==0){
           $orderInfo=M_Mysqli_Class('cro', 'XiuOrdersModel')->getOrderInfoById($parames['order_id']);
           //$tagArray=array(array("id"=>"1","name"=>"2131"),array("id"=>"2","name"=>"2133131"));
           if(count($orderInfo)>0){
               $parames['shop_user_id']=$orderInfo['shop_user_id'];
               $parames['shop_id']=$orderInfo['shop_id'];
               $parames['fix_id']=$orderInfo['fix_id'];
               $tagArray=json_decode($parames['tag'],true);
               $tagIdArray=[];
               foreach($tagArray as $k=>$v){
                   $tagIdArray[]=$v['id'];
               }
               $tagArray= !empty($tagIdArray) ? M_Mysqli_Class('cro', 'StarTagModel')->getStarTagByAttr(array("id"=>$tagIdArray)) : [];

               if(count($tagArray)==count($tagIdArray)){
                   $orderEvaluationId=M_Mysqli_Class('cro', 'OrderEvaluationModel')->addOrderEvaluation($parames);
                   if($orderEvaluationId>0){
                       $outPut['status']="ok";
                       $outPut['code']="2000";
                       $outPut['msg']="评价成功";
                       $outPut['data']="";
                   }else{
                       $outPut['status']="error";
                       $outPut['code']="0118";
                       $outPut['msg']="网络问题，请重新提交";
                       $outPut['data']="";
                   }
               }
           }
       }else{
           $outPut['status']="error";
           $outPut['code']="1040";
           $outPut['msg']="不可重复评价";
           $outPut['data']="";
       }
       return $outPut;
   }
    
}