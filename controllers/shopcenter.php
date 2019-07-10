<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 * 快修 商家中心
 */
class shopcenter extends MY_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->parames=$this->getParames();//调用http流方法
    }

    /**
     * get方法 获取商家信息
     * 
     */
    public function actionGet()
    {
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionGetShop"://获取店铺信息
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('shop_id','店铺表id shop_id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
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
                    $outPut['data']=$this->shopInfoFunction($parames);
                }
                break;
            case "actionShopHomePage"://获取商家端首页信息
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','商家用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
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
                    $outPut['data']=$this->ShopHomePageFunction($parames);
                }
                break;
            case "actionTodayShopOrders"://获取商家端首页当日订单
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','商家用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('shop_id','商家Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('page','页码','numeric|min_length[1]|required');
                $this->form_validation->set_rules('pageSize','每页条数','numeric|min_length[1]|required');
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
                    $outPut['data']=$this->todayShopOrdersFunction($parames);
                }
                break;
            case "actionTodayXgTotal"://获取商家端首页当日修哥总计
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','商家用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('shop_id','商家Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
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
                    $outPut['data']=$this->todayXgTotalFunction($parames);
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
                case "actionShopApply"://商家完善信息(入驻)
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('user_id','商家Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('name','店铺名称','trim|required');
                        $this->form_validation->set_rules('user_name','店铺所有人姓名','trim|required');
                        $this->form_validation->set_rules('phone','联系电话','trim|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('province_id','省份id','trim|required');
                        $this->form_validation->set_rules('province_name','省份','trim|required');
                        $this->form_validation->set_rules('city_id','市id','trim|required');
                        $this->form_validation->set_rules('city_name','市','trim|required');
                        $this->form_validation->set_rules('district_id','地区id','trim|required');
                        $this->form_validation->set_rules('district_name','地区','trim|required');
                        $this->form_validation->set_rules('city','详细地址','trim|min_length[1]|max_length[100]|required');
                        $this->form_validation->set_rules('brands','营业品牌','trim|min_length[1]|required');
                        $this->form_validation->set_rules('services','服务项目','trim|min_length[1]|required');
                        $this->form_validation->set_rules('charter_img','营业执照图片路径','trim|min_length[1]|required');
                        $this->form_validation->set_rules('dianpu_img','店铺照片路径','trim|min_length[1]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $this->_checkShopName($parames['name']);//验证商家名是否重复
                            $outPut=$this->addShopFunction($parames);//添加新商家数据
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
     * [actionPut 商家中心put方法]
     * @return [type] [description]
     */
    public function actionPut()
    {
                $parames=$this->parames;
                switch ($parames['action'])
                {
                    case "actionShopUpdate"://商家编辑
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('user_id','商家Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('shop_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('phone','联系电话','trim|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('province_id','省份id','trim|required');
                        $this->form_validation->set_rules('province_name','省份','trim|required');
                        $this->form_validation->set_rules('city_id','市id','trim|required');
                        $this->form_validation->set_rules('city_name','市','trim|required');
                        $this->form_validation->set_rules('district_id','地区id','trim|required');
                        $this->form_validation->set_rules('district_name','地区','trim|required');
                        $this->form_validation->set_rules('city','详细地址','trim|min_length[1]|max_length[100]|required');
                        $this->form_validation->set_rules('brands','营业品牌','trim|min_length[1]|required');
                        $this->form_validation->set_rules('services','服务项目','trim|min_length[1]|required');
                        $this->form_validation->set_rules('charter_img','营业执照图片路径','trim|min_length[1]|required');
                        $this->form_validation->set_rules('dianpu_img','店铺照片路径','trim|min_length[1]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $outPut=$this->upShopBrandServiceDeal($parames);
                        }
                        break;
                    case "actionShopFixStatusUpdate"://商家休息营业状态
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','商家Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('shop_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('fix_status','营业状态','numeric|min_length[1]|max_length[50]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $data=[];
                            $data['id']=$parames['shop_id'];
                            $data['fix_status']=$parames['fix_status'];
                            $outPut=$this->updateShopFunction($data);//修改营业状态
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
     * [todayXgTotalFunction 商家当日每个修哥的总订单量 和总金额]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    public function todayXgTotalFunction($parames)
    {
        //获取店铺下的修哥
        $xgList=M_Mysqli_Class('cro', 'UserModel')->getConditionUser(['parent_id'=>$parames['shop_id'],'role_flag'=>3,'status'=>1]);
        if (empty($xgList)) {
            return ;
        }
        $keyArray=array('id','name');
        foreach ($xgList as $k => $v) {
            $xgList[$k]=$this->setArray($keyArray, $xgList[$k]);
        }
        $xg_id_list=array_column($xgList,'id');
        $start_time=strtotime(date('Y-m-d',time()));
        // $start_time=1502098100;
        $end_time=strtotime('tomorrow');
        // $end_time=1503198100;
        $res=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTimeXgTotal(['shop_id'=>$parames['shop_id'],'order_status'=>3],$xg_id_list,$start_time,$end_time);//获取商家下每个修哥的当日有效营业额和订单量
        if (empty($res)) {//没有订单数据  全部返回空
            foreach ($xgList as $k => $v) {
                $xgList[$k]['xg_total_orders']='';
                $xgList[$k]['xg_total_order_amount']='';
            }
            return $xgList;
        }
        $res_fix_id_list=array_column($res,'fix_id');//获取有当日订单数据的修哥列表
        //有数据则将对应的数据 赋给对应的修哥
        foreach ($xgList as $k => $v) {
            foreach ($res as $s => $j) {
                if (!in_array($v['id'],$res_fix_id_list)) {//修哥没有当日数据 返回空
                    $xgList[$k]['xg_total_orders']='';
                    $xgList[$k]['xg_total_order_amount']='';
                }
                if ($v['id']==$j['fix_id']) {//有 则返回当日订单量和订单总额
                    $xgList[$k]['xg_total_orders']=$j['xg_total_orders'];
                    $xgList[$k]['xg_total_order_amount']=$j['xg_total_order_amount'];
                }
            }
        }
        return $xgList;
    }

    /**
     * [todayShopOrdersFunction 商家当日订单]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    public function todayShopOrdersFunction($parames)
     {
         $start_time=strtotime(date('Y-m-d',time()));
         // $start_time=1502098100;
         $end_time=strtotime('tomorrow');
         // $end_time=1502162299;
         $orderList=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTimeShopOrderList(['shop_id'=>$parames['shop_id']],$start_time,$end_time,$parames['page'],$parames['pageSize']);
         $keyArray=array(
                 'id',
                 'order_num',
                 'user_id',
                 'contact_mobile',
                 'contact_name',
                 'user_location_longitude',
                 'user_location_latitude',
                 'user_address',
                 'service_id',
                 'service_name',
                 'fix_id',
                 'fix_name',
                 'shop_id',
                 'shop_name',
                 'brand_id',
                 'brand_name',
                 'car_model_id',
                 'car_model_name',
                 'order_type',
                 'order_amount',
                 'create_date',
                 'booking_date',
                 'pay_date',
                 'order_status'
             );
         if(empty($orderList)){
            return;
         }
         $fixIdList=array_column($orderList, 'fix_id'); //获取修哥id数组集合
         $fixIdList=array_filter($fixIdList);//修哥id数组集合去空值
         $fixIconList=M_Mysqli_Class('cro', 'ImgModel')->getXgIconList($fixIdList,1,1);
         foreach($orderList as $k=>$v){
             $orderList[$k]=$this->setArray($keyArray, $v);
                 if (!empty($fixIconList)) {
                     foreach ($fixIconList as $s => $j) {
                         if ($j['attr_id']==$v['fix_id']) {
                             $orderList[$k]['xg_icon']=['hash'=>$j['img_hash'],'key'=>$j['img_key']];
                         }else{
                             $orderList[$k]['xg_icon']=['hash'=>'','key'=>''];
                         }
                     }
                 }else{
                     $orderList[$k]['xg_icon']=['hash'=>'','key'=>''];
                 }
         }
         return $orderList;
     } 

    /**
     * [ShopHomePageFunction 商家首页]
     * @param [type] $parames [description]
     */
    private function ShopHomePageFunction($parames)
    {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        $userInfo = M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr(['id'=>$parames['user_id']]);
        $shopInfo=[];
        $shopInfo['id']='';
        $shopInfo['name']='';
        $shopInfo['dianpu_img']='';
        $shopInfo['fix_status']='';
        $shopInfo['shop_apply_status']='-1';
        $shopInfo['realname_status']=$userInfo['realname_status'];
        $shopInfo['total_order_num']='';
        $shopInfo['total_shop_amount']='';
        $shopInfo['today_order_num']='';
        $shopInfo['today_shop_amount']='';
        $shopData = M_Mysqli_Class('cro', 'ShopModel')->getShopInfoByAttr($parames);
        if (!empty($shopData)) {
            $shopInfo['id']=$shopData['id'];//商家表id
            $shopInfo['name']=$shopData['name'];
            $shopInfo['dianpu_img']=$shopData['dianpu_img'];//商家店铺图
            $shopInfo['fix_status']=$shopData['fix_status'];//营业状态
            $shopInfo['shop_apply_status']=$shopData['status'];//入驻状态
            $shopInfo['dianpu_img']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$shopInfo['id'],'attr'=>2,'type'=>4]);//店铺图片
            //营业总额
            $shop_total=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTotalShopOrder($shopInfo['id'],3);
            $shopInfo['total_order_num']=$shop_total['total_order_num'];
            $shopInfo['total_shop_amount']=$shop_total['total_shop_amount'];
            //当日营业总额
            $start_time=strtotime(date('Y-m-d',time()));
            $end_time=strtotime('tomorrow');
            $today_shop_total=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTodayShopOrder($shopInfo['id'],3,$start_time,$end_time);
            $shopInfo['today_order_num']=$today_shop_total['today_order_num'];
            $shopInfo['today_shop_amount']=$today_shop_total['today_shop_amount'];
        }
        unset($shopData);//销毁无用数据
        return $shopInfo;
    }

    /**
     * [shopInfoFunction 获取单条商家信息方法]
     * @param [type] $parames [description]
     */
    public function shopInfoFunction($parames)
    {
        unset($parames['token']);//去除多余数据
        $parames['id']=$parames['shop_id'];
        unset($parames['shop_id']);
        unset($parames['user_id']);
        unset($parames['action']);
        $shopInfo = M_Mysqli_Class('cro', 'ShopModel')->getShopInfoByAttr($parames);//通过店铺所属用户id获取商家基本信息
        $keyArray=array("id","user_id","name","user_name","phone","city","province_id","province_name","city_id","city_name","district_id","district_name","status","fix_status");//商家基本信息
        $brandCondition=['shop_id'=>$shopInfo['id'],'status'=>1,'type'=>1];//商家经营品牌检索条件
        $serviceCondition=['shop_id'=>$shopInfo['id'],'status'=>1,'type'=>2];//商家提供服务检索条件
        $shopInfo=$this->setArray($keyArray,$shopInfo);
        $shopInfo['dianpu_img']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$shopInfo['id'],'attr'=>2,'type'=>4]);//店铺图片
        $shopInfo['charter_img']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$shopInfo['id'],'attr'=>2,'type'=>5]);//营业执照图
        $brandData = M_Mysqli_Class('cro', 'ShopAttrCenterModel')->getShopAttrCenterInfoByAttr($brandCondition);//获取商家经营品牌
        $keyArray=array("id","shop_id","attr_id","attr_name","status");
        $serviceData = M_Mysqli_Class('cro', 'ShopAttrCenterModel')->getShopAttrCenterInfoByAttr($serviceCondition);//获取商家提供服务
        foreach ($brandData as $k => $v) 
        {
            $shopInfo['brandData'][$k]=$this->setArray($keyArray,$v);
        }
        foreach ($serviceData as $k => $v) 
        {
            $shopInfo['serviceData'][$k]=$this->setArray($keyArray,$v);
        }
        return $shopInfo;
    }

    /**
     * [_checkShopName 验证商家名]
     * @return [type] [description]
     */
    private function _checkShopName($shopname)
    {
         $row=M_Mysqli_Class('cro', 'ShopModel')->checkShopName($shopname);
         if($row>0){
             $outPut['status'] = 'error';
             $outPut['code']="1700";
             $outPut['msg'] = '该商家已存在,请更换商家名';
             $outPut['data'] = '';
             $this->setOutPut($outPut);
         }else{
            return true;
         }
    }

    /**
     * [addShopFunction 添加商家]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function addShopFunction($parames)
    {
        $brands=json_decode($parames['brands'],true);
        $this->_checkBrandsQuantity($brands);//验证品牌数量
        $services=json_decode($parames['services'],true);
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        unset($parames['brands']);//去除多余数据
        unset($parames['services']);//去除多余数据
        $dianpu_img=json_decode($parames['dianpu_img'],true);
        $charter_img=json_decode($parames['charter_img'],true);
        unset($parames['dianpu_img']);//去除多余数据
        unset($parames['charter_img']);//去除多余数据
        $parames['createtime']=time();
        $parames['createip']= $this->getClientIP();
        M_Mysqli_Class('cro', 'ShopModel')->trans_start();
        $newShopId=M_Mysqli_Class('cro', 'ShopModel')->addShop($parames);//商家数据
        $total_data=[];
        $merge_data=array_merge($brands,$services);
        foreach ($merge_data as $k => $v) 
        {
           $total_data[$k]=$v;
           $total_data[$k]['shop_id']=$newShopId;
           $total_data[$k]['create_time']=time();
           $total_data[$k]['create_date']=date('Y-m-d-H-i-s',time());
        }
        M_Mysqli_Class('cro', 'ShopAttrCenterModel')->addShopAttrCenterPatch($total_data);//服务和品牌数据
            //插入图片表数据
        $imgData=[];
        $imgData[0]['attr_id']=$newShopId;//用户id
        $imgData[0]['attr']=2;//商家
        $imgData[0]['type']=4;//店铺照片
        $imgData[0]['img_key']=$dianpu_img['key'];//图片key
        $imgData[0]['img_hash']=$dianpu_img['hash'];
        $imgData[0]['create_time']=time();
        $imgData[0]['create_date']=date('Y-m-d,H:i:s',time());
        $imgData[1]['attr_id']=$newShopId;//商家id
        $imgData[1]['attr']=2;//商家
        $imgData[1]['type']=5;//营业执照
        $imgData[1]['img_key']=$charter_img['key'];
        $imgData[1]['img_hash']=$charter_img['hash'];
        $imgData[1]['create_time']=time();
        $imgData[1]['create_date']=date('Y-m-d,H:i:s',time());
        M_Mysqli_Class('cro', 'ImgModel')->addImgPatch($imgData);//图片数据
        M_Mysqli_Class('cro', 'UserModel')->trans_complete();
        $trans_status=M_Mysqli_Class('cro', 'ImgModel')->trans_status();
        if ($trans_status) {
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="入驻成功";
            $outPut['data']="";
        } else {
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="入驻失败";
            $outPut['data']="";
        }
        unset($imgData);
        unset($total_data);
        return $outPut;
    }


   /**
    * [upShopBrandServiceDeal 更新商家信息 品牌和服务信息]
    * @return [type]          [description]
    */
   public function upShopBrandServiceDeal($parames)
   {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        $brands=json_decode($parames['brands'],true);
        $this->_checkBrandsQuantity($brands);//验证品牌数量,不超过三个
        $services=json_decode($parames['services'],true);
        $keyArray=array("phone","province_id","province_name","city_id","city_name","district_id","district_name","city");
        $data=$this->setArray($keyArray,$parames);//过滤数据 组合商家表更新数据
        $data['id']=$parames['shop_id'];//
        $data['updatetime']=time();//更新时间
        $data['updateip']=$this->getClientIP();//更新ip
        $dianpu_img=json_decode($parames['dianpu_img'],true);//店铺门头图片数据转换格式
        $charter_img=json_decode($parames['charter_img'],true);//店铺营业执照图片数据转换格式
        $total_data=[];//存放品牌和服务数组
        $merge_data=array_merge($brands,$services);
        foreach ($merge_data as $k => $v) //组合批量插入数组
        {
           $total_data[$k]=$v;
           $total_data[$k]['shop_id']=$parames['shop_id'];
           $total_data[$k]['create_time']=time();
           $total_data[$k]['create_date']=date('Y-m-d-H-i-s',time());
        }
        //店铺图片数组 
        $dianpu_imgData=[];
        $dianpu_imgData['attr_id']=$parames['shop_id'];//商家id
        $dianpu_imgData['attr']=2;//商家
        $dianpu_imgData['type']=4;//店铺图
        $dianpu_imgData['img_key']=$dianpu_img['key'];//图片key
        $dianpu_imgData['img_hash']=$dianpu_img['hash'];
        //店铺营业执照数组
        $charter_imgData=[];
        $charter_imgData['attr_id']=$parames['shop_id'];//商家id
        $charter_imgData['attr']=2;//商家
        $charter_imgData['type']=5;//营业执照
        $charter_imgData['img_key']=$charter_img['key'];//图片key
        $charter_imgData['img_hash']=$charter_img['hash'];
        M_Mysqli_Class('cro', 'ShopModel')->trans_start();
        $this->updateShopFunction($data);//更新商家数据
        //判断是否需要更新店铺图和营业执照图
        if ($dianpu_imgData['img_hash']!='') { //更新了cro的老图 则插入或更新数据,没更新头像则不插入图片表新数据
            $dianpu_imgOldData=M_Mysqli_Class('cro', 'ImgModel')->getImgInfoByAttr(['attr_id'=>$parames['shop_id'],'attr'=>2,'type'=>4]);//店铺门头照片旧
            if (empty($dianpu_imgOldData)) {//没有数据则插入
                $dianpu_imgData['create_time']=time();
                $dianpu_imgData['create_date']=date('Y-m-d,H:i:s',time());
                M_Mysqli_Class('cro', 'ImgModel')->addImg($dianpu_imgData);
            }else{//有则更新
                $dianpu_imgData['id']=$dianpu_imgOldData['id'];
                $dianpu_imgData['update_time']=time();
                $dianpu_imgData['update_date']=date('Y-m-d,H:i:s',time());
                M_Mysqli_Class('cro', 'ImgModel')->updateImg($dianpu_imgData);
            }
        }
        if ($charter_imgData['img_hash']!='') { //更新了cro的老图 则插入或更新数据,没更新头像则不插入图片表新数据
            $charter_imgOldData=M_Mysqli_Class('cro', 'ImgModel')->getImgInfoByAttr(['attr_id'=>$parames['shop_id'],'attr'=>2,'type'=>5]);//店铺营业执照旧
            if (empty($charter_imgOldData)) {//没有数据则插入
                $charter_imgData['create_time']=time();
                $charter_imgData['create_date']=date('Y-m-d,H:i:s',time());
                M_Mysqli_Class('cro', 'ImgModel')->addImg($charter_imgData);
            }else{//有则更新
                $charter_imgData['id']=$charter_imgOldData['id'];
                $charter_imgData['update_time']=time();
                $charter_imgData['update_date']=date('Y-m-d,H:i:s',time());
                M_Mysqli_Class('cro', 'ImgModel')->updateImg($charter_imgData);
            }
        }
        //删除旧品牌和服务
        M_Mysqli_Class('cro', 'ShopAttrCenterModel')->deleteShopAttrCenterPatch(['shop_id'=>$parames['shop_id']]);
        M_Mysqli_Class('cro', 'ShopAttrCenterModel')->addShopAttrCenterPatch($total_data);//新增品牌服务
        M_Mysqli_Class('cro', 'ShopModel')->trans_complete();
        $trans_status=M_Mysqli_Class('cro', 'ShopModel')->trans_status();
        if ($trans_status) {
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="更新成功";
            $outPut['data']="";
        } else {
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="更新失败";
            $outPut['data']="";
        }
        return $outPut;
   }

   /**
    * [_checkBrandsQuantity 商家绑定品牌数量不可超过三个]
    * @param  [type] $brands [description]
    * @return [type]         [description]
    */
   private function _checkBrandsQuantity($brands)
   {
        if(count($brands)>3){
            $outPut['status'] = 'error';
            $outPut['code']="1100";
            $outPut['msg'] = '商家绑定品牌数量不可超过三个';
            $outPut['data'] = '';
            $this->setOutPut($outPut);
        }
        return true;
   }

    /**
     * [updateUserFunction 更新商家信息]
     * @param  [type] $data [description]
     * @return [type]          [description]
     */
    private function updateShopFunction($data)
    {
        $data['updatetime']=time();
        $data['updateip']= $this->getClientIP();
        $res=M_Mysqli_Class('cro', 'ShopModel')->updateShop($data);
        if ($res) {
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="操作成功";
            $outPut['data']="";
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="操作失败";
            $outPut['data']="";
        }
        return $outPut;
    }

}

?>