<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class product extends MY_Controller {

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
            case "actionGetService"://获取服务列表
                $list=M_Mysqli_Class('cro', 'XiuServiceModel')->getAllService(0);
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="";
                if(is_array($list)){
                   // $keyArray=array("id","service_name","content","icon","price");
                   $keyArray=array("id","service_name","content","icon","price","service_type");//9.22增加服务类型
                   foreach($list as $k=>$v){
                      $list[$k]=$this->setArray($keyArray,$v);
                   }
                }
                $outPut['data']=$list;
                break;
            case "actionGetBrand"://获取品牌列表
                if(!array_key_exists('name',$parames)){
                    $parames['name']="";
                }
                $list=M_Mysqli_Class('cro', 'BrandModel')->getAllBrand(0,$parames['name']);
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="";
                if(is_array($list)){
                    $keyArray=array("id","brand_name","capital");
                    foreach($list as $k=>$v){
                        $list[$k]=$this->setArray($keyArray,$v);
                    }
                }
                $outPut['data']=$list;
                break;
            case "actionGetCarModel"://获取品牌车辆型号列表
                if(!array_key_exists('name',$parames)){
                    $parames['name']="";
                }
                $list=M_Mysqli_Class('cro', 'CarModelModel')->getAllCarModel(0,$parames['brand_id'],$parames['name']);
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="";
                if(is_array($list)){
                    $keyArray=array("id","car_model_name","brand_id");
                    foreach($list as $k=>$v){
                        $list[$k]=$this->setArray($keyArray,$v);
                    }
                }
                $outPut['data']=$list;
                break;
            case "actionGetCity"://获取城市列表
                if(!array_key_exists('name',$parames)){
                    $parames['name']="";
                }
                $list=M_Mysqli_Class('cro', 'CityModel')->getAllCity($parames['name']);
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="";
                if(is_array($list)){
                    $keyArray=array("CityID","CityName","Initial","ProvinceID");
                    foreach($list as $k=>$v){
                        $list[$k]=$this->setArray($keyArray,$v);
                    }
                }
                $outPut['data']=$list;
                break;
            case "actionTestMemcache":
                $mem = new Memcache();
                $mem->addserver('127.0.0.1',11211);
                $data=M_Mysqli_Class('cro', 'BrandModel')->getAllBrand(0);
                $mem->set('test', $data);
                $val=$mem->get('test');
                print_r($val);
                exit;
                break;
            case "actionTestRedis":
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379);
                $data=M_Mysqli_Class('cro', 'XiuOrdersModel')->getOrderFieldById(['id','fix_id','order_status'],69);
                // var_dump($data);exit;
                $redis->set('data',json_encode($data));
                $val=$redis->get('data');
                print_r($val);exit;
                break;

            default:
                $outPut['status']="error";
                $outPut['code']="4040";
                $outPut['msg']="请求错误";
                $outPut['data']="";
        }
        $this->setOutPut($outPut);
    }
    
    public function actionPost(){
        $parames=$this->parames;
        print_r($parames);exit;
    }
    
 
}