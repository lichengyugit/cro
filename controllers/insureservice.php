<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 * 保险服务
 */
class insureservice extends MY_Controller {

    private $dqInsureId=1;//盗抢险id

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
            case "actionDqInsureList"://获取用户盗抢险信息
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
                    $outPut=$this->DqInsureListFunction($parames);
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
     * [DqInsureListFunction 获取用户盗抢险信息]
     * @param [type] $parames [description]
     */
    private function DqInsureListFunction($parames)
    {
        $confdition=[];
        $confdition['insure_id']=$this->dqInsureId;//激活保险为盗抢险
        $confdition['status']=0;//未激活(car_insure表)
        $confdition['pay_status']=1;//已付款
        $confdition['user_id']=$parames['user_id'];//用户id
       $res= M_Mysqli_Class('cro', 'CroOrdersModel')->getDqInsureOrderInfo($confdition);
       if (empty($res)) {
           $outPut['status']="error";
           $outPut['code']="5001";
           $outPut['msg']="暂无需要激活的保险";
           $outPut['data']="";
       } else {
           $outPut['status']="ok";
           $outPut['code']="2000";
           $outPut['msg']="";
           $outPut['data']=$res;
       }
       return $outPut;
    }

}
?>
