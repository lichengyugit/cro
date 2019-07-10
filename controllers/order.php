<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class order extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();
    }
    
    /**
     * post方法
     */
    public function actionPost(){
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionCreateOrder"://创建快修订单
                //$this->_checkUserLogin(3070, "5da3c3840947c121416fb760749890b0");
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('user_name','用户名','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('contact_mobile','订单联系人手机号','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('contact_name','订单联系人名称','trim|min_length[1]|max_length[50]|');
                $this->form_validation->set_rules('user_location','用户坐标','trim|min_length[1]|max_length[100]');
                $this->form_validation->set_rules('user_address','用户所在地址','trim|min_length[1]|max_length[100]|required');
                //$this->form_validation->set_rules('province_id','省id','numeric|min_length[1]|max_length[11]');
                $this->form_validation->set_rules('province_name','省名称','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('city_id','市id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('city_name','市名称','trim|min_length[1]|max_length[50]|required');
                //$this->form_validation->set_rules('district_id','区县id','numeric|min_length[1]|max_length[11]');
                $this->form_validation->set_rules('district_name','区县名称','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('service_id','快修项目Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('service_name','快修项目名称','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('brand_id','品牌Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('brand_name','品牌名称','trim|min_length[1]|max_length[50]|required');
//                 $this->form_validation->set_rules('car_model_id','车辆型号Id','numeric|min_length[1]|max_length[11]');
//                 $this->form_validation->set_rules('car_model_name','车辆型号名称','trim|min_length[1]|max_length[50]');
                $this->form_validation->set_rules('order_type','订单类型','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('order_amount','订单金额','numeric|min_length[1]|max_length[10]|required');
                $this->form_validation->set_rules('booking_date','预约时间','trim|min_length[1]|max_length[20]');
                $this->form_validation->run();
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $outPut=$this->createOrderFunction($parames);
                }
                break;
            case "actionOrderStatus"://操作订单状态
                //$this->_checkUserLogin(3070, "5da3c3840947c121416fb760749890b0");
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('order_id','订单id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('order_status','操作状态','numeric|min_length[1]|max_length[5]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $outPut=$this->orderStatusFunction($parames);
                }
                break;
            // case "actionFixReceiveOrder"://修哥接单
            //     $this->form_validation->set_data($parames);
            //     $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
            //     $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
            //     $this->form_validation->set_rules('user_location','用户位置','trim|min_length[1]|max_length[100]|required');
            //     if ($this->form_validation->run() === FALSE) {
            //         $outPut['status']="error";
            //         $outPut['code']="1001";
            //         $outPut['msg']=$this->form_validation->validation_error();
            //         $outPut['data']="";
            //     }else{
            //         $this->_checkUserLogin($parames['user_id'], $parames['token']);
            //         $parames['radius']=2;//半径可配置单位KM
            //         $outPut['status']='ok';
            //         $outPut['code']="2000";
            //         $outPut['msg']='';
            //         $outPut['data']=$this->fixReceiveOrderFunction($parames);
            //     }
            //     break;
            case "actionAutoSendOrder"://自动派单
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('order_id','订单id','trim|min_length[1]|max_length[100]|required');
                // $this->form_validation->set_rules('brand_id','品牌id','trim|min_length[1]|max_length[100]|required');
                $this->form_validation->set_rules('brand_name','品牌名称','trim|min_length[1]|max_length[100]|required');
                // $this->form_validation->set_rules('service_id','服务项目id','trim|min_length[1]|max_length[100]|required');
                $this->form_validation->set_rules('service_name','服务名','trim|min_length[1]|max_length[100]|required');
                $this->form_validation->set_rules('user_location','用户位置','trim|min_length[1]|max_length[100]|required');
                $this->form_validation->set_rules('user_address','用户地址','trim|min_length[1]|required');
                $this->form_validation->set_rules('order_type','订单类型','trim|min_length[1]|max_length[100]|required');
                $this->form_validation->set_rules('create_date','创建时间','trim|min_length[1]|max_length[100]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $parames['radius']=3;//半径可配置，单位KM
                    $outPut=$this->autoSendOrderFunction($parames);
                }
                break;
            case "actionPostFixOrderList"://获取订单列表
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户ID','trim|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('attr_id','对应类型的id','trim|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('attr','端口类型 1用户端订单 2商家端订单 3修哥订单 4代理商订单','trim|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('order_status','订单状态','trim');
                $this->form_validation->set_rules('page','页码','numeric|min_length[1]|required');
                $this->form_validation->set_rules('pageSize','每页条数','numeric|min_length[1]|required');
                $this->form_validation->run();
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $outPut['status']='ok';
                    $outPut['code']="2000";
                    $outPut['msg']='';
                    $outPut['data'] =$this->OrderListFunction($parames,$parames['page'], intval($parames['pageSize']));
                }
                break;
            case 'actionTimeoutAutoFinish'://超时订单自动完成
                $outPut['status']='ok';
                $outPut['code']="200";
                $outPut['msg']='';
                $outPut['data'] =$this->TimeoutAutoFinishFunction();
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
     * get方法
     */
    public function actionGet(){
        $parames=$this->input->get();
        switch ($parames['action'])
        {
            case "actionGetOrderInfo"://获得订单详情
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('order_id','订单id','numeric|min_length[1]|max_length[11]|required');
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
                    $outPut['data']=$this->OrderInfoFunction($parames);
                }
                break;
            case "actionTestPush"://push测试
                $return = F()->Jpush_module->send('3931',array('2988'),'test22',array('android'),'fix');
                print_r($return);exit;
                break;
//             case "actionTestPush"://push测试
//                 $return = F()->Jpush_module->send('3931',array('3929'),'test');
//                 print_r($return);exit;
//                 break;
            default:
                $outPut['status']="error";
                $outPut['code']="4040";
                $outPut['msg']="请求错误";
                $outPut['data']="";
        }
        $this->setOutPut($outPut);
    }
    
    /**
     * [autoSendOrderFunction 自动派单]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function autoSendOrderFunction($parames)
    {
        unset($parames['action']);
        unset($parames['token']);
        // $lo='121.523393,31.272592';
        $fixLocationArray=explode(',',$parames['user_location']);
        // $fixLocationArray=explode(',',$lo);
        // $autoFixId=M_Mysqli_Class('cro', 'FixStatusModel')->atuoNearFix($fixLocationArray,$parames['radius'],0,1,1,$parames['brand_id'],$parames['service_id']);//用户位置 搜索半径3KM 修哥接单状态(未接单) 修哥开工状态(开工) 修哥所属商家营业状态(营业) ,车辆品牌id 服务项目id ----商家
        $autoFixInfo=M_Mysqli_Class('cro', 'FixStatusModel')->atuoNearAgentFix($fixLocationArray,$parames['radius'],0,1);//用户位置 搜索半径3KM 修哥接单状态(未接单) 修哥开工状态(开工) -----代理商
        $autoFixId=$autoFixInfo['fix_id'];
        // var_dump($autoFixInfo);exit;
        if (!empty($autoFixInfo)) 
        {
            $fix_phone=$this->FixReceiveOrderFunction(['fix_id'=>$autoFixId,'id'=>$parames['order_id']]);//调用修哥接单方法 传入修哥id 和订单id
            $contentArr = [
                                        'type' => '2',
                                        'data' => [
                                            "orderId"=>$parames['order_id'],
                                            "brand_name" => $parames['brand_name'],
                                            "user_address" => $parames['user_address'],
                                            "user_location" => $parames['user_location'],
                                            "order_type" => $parames['order_type'],
                                            "service_name" => $parames['service_name'],
                                            "create_date" => $parames['create_date'],
                                            "allot" =>'2'
                                        ]
                                    ];
            $content=json_encode($contentArr);
            F()->Jpush_module->send($parames['user_id'],array($autoFixId),$content,array('android'),'fix');//将订单推送给修哥
            $voice_data=[];
            $voice_data['mobile']=$fix_phone;
            $voice_data['content']='您有新的订单，请及时处理。';
            F()->Voice_message_module->sendVoiceMessage($voice_data);//电话通知
            $outPut['status']='ok';
            $outPut['code']="2000";
            $outPut['msg']='派单成功';
            $outPut['data']='';
        }else{
            $outPut['status']='error';
            $outPut['code']="1050";
            $outPut['msg']='暂无符合条件的修哥,请等待';
            $outPut['data']='';
        }
        return $outPut;
    }

    /**
     * [FixReceiveOrderFunction 修哥接单方法]
     * @param [type] $parames [description]
     */
    private function FixReceiveOrderFunction($parames)
    {
            $fixInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['fix_id']]);//修哥用户信息
            // $shopInfo = M_Mysqli_Class('cro','ShopModel')->getShopInfoByAttr(['id'=>$fixInfo['parent_id']]);//商家信息
            $agentInfo = M_Mysqli_Class('cro','XiuAgentModel')->getAgentInfoByAttr(['id'=>$fixInfo['parent_id']]);//代理商信息
            $fixStatus=M_Mysqli_Class('cro','FixStatusModel')->getFixInfoByFixId($parames['fix_id']);//修哥状态信息
            $orderInfo = M_Mysqli_Class('cro','XiuOrdersModel')->getOrderFieldById(['id','order_type','order_status'],$parames['id']);//订单信息,订单状态,订单种类
            if ($fixStatus['order_status']=='1' && $orderInfo['order_type']=='0') {//如果修哥已接单并且订单为实时订单
                $outPut['status']="error";
                $outPut['code']="3002";
                $outPut['msg']="派单失败";
                $outPut['data']="";
                $this->setOutPut($outPut);
            }
            if ($orderInfo['order_status']!='0') {
                $outPut['status']="error";
                $outPut['code']="3002";
                $outPut['msg']="派单失败";
                $outPut['data']="";
                $this->setOutPut($outPut);
            }
            $orderUpdate = [
                                    // 'id'=>$parames['id'],
                                    'fix_id'=>$parames['fix_id'],
                                    'fix_name'=>$fixInfo['name'],
                                    'fix_phone' => $fixInfo['tel'],
                                    // 'shop_id'=> $fixInfo['parent_id'],
                                    // 'shop_name' =>$shopInfo['name'],
                                    // 'shop_user_id' => $shopInfo['user_id'],
                                    'agent_id'=> $fixInfo['parent_id'],
                                    'agent_name' =>$agentInfo['name'],
                                    'agent_user_id' => $agentInfo['user_id'],
                                    'order_status' => '1',
                                    // 'shop_location_longitude' => $shopInfo['lng'],
                                    // 'shop_location_latitude' => $shopInfo['lat'],
                                    'fix_location_longitude' => $fixStatus['fix_location_longitude'],
                                    'fix_location_latitude' => $fixStatus['fix_location_latitude'],
                                    'receive_time' => time(),
                                    'receive_date' => date('Y-m-d H:i:s'),
                                ];
            M_Mysqli_Class('cro','XiuOrdersModel')->trans_start();//事务开启
            M_Mysqli_Class('cro','XiuOrdersModel')->updateOrderAttrs($orderUpdate,['id'=>$parames['id'],'order_status'=>'0']);//修改订单
           if ($orderInfo['order_type']=='0')//订单类型为实时订单时 修改修哥状态 ,为预约单时不更改修哥状态
           {
                M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['order_status'=>'1'],['fix_id'=>$parames['fix_id'],'order_status'=>'0']);//修哥接单状态变为已接单
            }
            M_Mysqli_Class('cro','XiuOrdersModel')->trans_complete();
            unset($fixInfo);
            unset($agentInfo);
            unset($orderInfo);
            if(M_Mysqli_Class('cro','XiuOrdersModel')->trans_status() != FALSE)
            {
               return $orderUpdate['fix_phone'];
            }else{
                $outPut['status']="error";
                $outPut['code']="0118";
                $outPut['msg']="网络问题，请重试";
                $outPut['data']="";
                $this->setOutPut($outPut);
            }
    }


    /**
     * 创建订单方法
     */
    private function createOrderFunction($parames)
    {
        unset($parames['action']);
        unset($parames['token']);
        $userWallet = M_Mysqli_Class('cro', 'UserWalletModel')->getWalletByUserId($parames['user_id']);
        if(count($userWallet)>0){
            if(($userWallet['balance']+$userWallet['giving_balance'])>=$parames['order_amount']){
                $num = M_Mysqli_Class('cro','XiuOrdersModel')->getAllFixOrders(['user_id'=>$parames['user_id'],'order_status'=>[2,1,0,5,6]]);
                if (count($num)>0) {
                    $outPut['status']="error";
                    $outPut['code']="1009";
                    $outPut['msg']="还有订单未完成，暂时无法发布订单";
                    $outPut['data']="";
                    return $outPut;
                }
                    $userLocationArray=explode(',', $parames['user_location']);
                    $parames['user_location_longitude']=$userLocationArray['0'];
                    $parames['user_location_latitude']=$userLocationArray['1'];
                    $user_location = $parames['user_location'];
                    unset($parames['user_location']);
                    $cityInfo = M_Mysqli_Class('cro', 'CityModel')->getCityById($parames['city_id']);
                    $provinceInfo = M_Mysqli_Class('cro', 'ProvinceModel')->getProvinceById($cityInfo['ProvinceID']);
                    $parames['province_id']=$provinceInfo['ProvinceID'];
                    $parames['province_name']=$provinceInfo['ProvinceName'];
                    $districtInfo = M_Mysqli_Class('cro', 'DistrictModel')->getOneDistrictByAttr(array("CityID"=>$parames['city_id'],"DistrictName"=>$parames['district_name']));
                    $parames['district_id']=$districtInfo['DistrictID'];
                    $parames['district_name']=$districtInfo['DistrictName'];
                    /*--------------------老版本不可使用保险激活服务--------------------------*/
                    $serviceInfo = M_Mysqli_Class('cro', 'XiuServiceModel')->getServerById($parames['service_id']);
                    $cro_order_id=empty($parames['cro_order_id'])?'':$parames['cro_order_id'];
                    unset($parames['cro_order_id']);//销毁无用变量
                    if ($serviceInfo['service_type']==2) {
                        if (empty($cro_order_id)) {
                            $outPut['status']="error";
                            $outPut['code']="6002";
                            $outPut['msg']="请升级到最新版本,享受此项服务";
                            $outPut['data']="";
                            $this->setOutPut($outPut);
                            exit;
                        }
                    }
                    /*----------------------------------------------*/
                    if($parames['order_type']==0){
                        unset($parames['booking_date']);
                        $parames['order_num']=$this->createOrderNum($parames,"XO");
                        $xiuOrderId = M_Mysqli_Class('cro', 'XiuOrdersModel')->addXiuOrder($parames);
                    }else{
                        $parames['booking_time']=strtotime($parames['booking_date']);
                        if($parames['booking_time']<time()){
                            $xiuOrderId="";
                        }else{
                            $parames['order_num']=$this->createOrderNum($parames,"XO");
                            $xiuOrderId = M_Mysqli_Class('cro', 'XiuOrdersModel')->addXiuOrder($parames);
                        }
                    }
                    if($xiuOrderId>0){
                        //验证服务
                        $checkServiceData=[];
                        $checkServiceData['xiu_order_id']=$xiuOrderId;//修铺订单id
                        $checkServiceData['cro_order_id']=$cro_order_id;//cro订单id
                        $checkServiceData['service_type']=$serviceInfo['service_type'];//服务类型
                        $this->checkService($checkServiceData);
                        // exit;
                        $fixList = M_Mysqli_Class('cro', 'FixStatusModel')->getNearFix($userLocationArray,3,'0','1');
                        $pushFixIdArray = F()->Jpush_module->operationIdArray($fixList,1);
                        $contentArr = [
                                                    'type' => '1',
                                                    'data' => [
                                                        "orderId"=>$xiuOrderId,
                                                        "brand_name" => $parames['brand_name'],
                                                        "user_address" => $parames['user_address'],
                                                        "user_location" => $user_location,
                                                        "order_type" => $parames['order_type'],
                                                        "service_name" => $parames['service_name'],
                                                        "create_date" => date('Y-m-d H:i:s'),
                                                        "allot" =>'1',
                                                    ]
                                                ];

                        $countent=json_encode($contentArr);
                        // $countent=json_encode(array("orderId"=>$xiuOrderId));
                        $pushReturn = F()->Jpush_module->pushUserOrderToFix($parames['user_id'],$pushFixIdArray,$countent,array('android'));
                        $data['order']=array('order_id'=>$xiuOrderId,"timing" => '30','timeOut' => '150','createDate'=>date('Y-m-d H:i:s'));
                        $data['pushReturn']=$pushReturn;
                        $outPut['status']="ok";
                        $outPut['code']="2000";
                        $outPut['msg']="下单成功";
                        $outPut['data']=$data;
                    }elseif($xiuOrderId==""){
                        $outPut['status']="error";
                        $outPut['code']="3003";
                        $outPut['msg']="当前日期不可预约";
                        $outPut['data']="";
                    }else{
                        $outPut['status']="error";
                        $outPut['code']="0118";
                        $outPut['msg']="网络问题，请重新下单";
                        $outPut['data']="";
                    } 
            }else{
                $outPut['status']="error";
                $outPut['code']="3004";
                $outPut['msg']="余额不足，请前往充值";
                $outPut['data']="";
            }
        }else{
            $outPut['status']="error";
            $outPut['code']="3004";
            $outPut['msg']="余额不足，请前往充值";
            $outPut['data']="";
        }
        unset($parames);
        return $outPut;
    }


    /**
     * [checkService 验证服务]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    public function checkService($parames)
    {
        if ($parames['service_type']==2) {
            if (empty($parames['cro_order_id'])) {
                $outPut['status']="error";
                $outPut['code']="6002";
                $outPut['msg']="请升级到最新版本,享受此项服务2";
                $outPut['data']="";
                $this->setOutPut($outPut);
                exit;
            }
     
            $checkRx=M_Mysqli_Class('cro', 'CroXiuOrderRelativeModel')->getCXRelativeByAttr(['cro_order_id'=>$parames['cro_order_id']]);//查询是否已存在cro订单和修铺订单关联信息
            if ($checkRx) 
            {
                //有则更新
                $cxRelativeData=[];
                $cxRelativeData['id']=$checkRx['id'];
                $cxRelativeData['xiu_order_id']=$parames['xiu_order_id'];
                $cxRelativeData['cro_order_id']=$parames['cro_order_id'];
                $return=M_Mysqli_Class('cro', 'CroXiuOrderRelativeModel')->updateCXRelative($cxRelativeData);
            }else{
                //没有则添加
                $cxRelativeData=[];
                $cxRelativeData['xiu_order_id']=$parames['xiu_order_id'];
                $cxRelativeData['cro_order_id']=$parames['cro_order_id'];
                $return=M_Mysqli_Class('cro', 'CroXiuOrderRelativeModel')->addCXRelative($cxRelativeData);//cro订单和修铺订单关联表
            }
            if (($return<0)||($return==false)) {
                $outPut['status']="error";
                $outPut['code']="0118";
                $outPut['msg']="网络问题，请取消订单后重新下单";
                $outPut['data']="";
                $this->setOutPut($outPut);
                exit;
            }
        }
    }
    
    /**
     * 操作订单状态
     * @param unknown $parames
     */
    private function orderStatusFunction($parames){
        unset($parames['action']);
        unset($parames['token']);
        $time=time();
        if($parames['order_status']==1){//接单
            $parames['receive_date']=date("Y-m-d H:i:s",$time);
            $parames['receive_time']=$time;
        }elseif($parames['order_status']==2){//已到达
            $parames['arrive_date']=date("Y-m-d H:i:s",$time);
            $parames['arrive_time']=$time;
        }elseif($parames['order_status']==3){//已完成
            $parames['complete_date']=date("Y-m-d H:i:s",$time);
            $parames['complete_time']=$time;
        }elseif($parames['order_status']==4){//已取消
            $parames['cancel_date']=date("Y-m-d H:i:s",$time);
            $parames['cancel_time']=$time;
        }
        $parames['id']=$parames['order_id'];
        unset($parames['order_id']);
        $actionUpdate = M_Mysqli_Class('cro', 'XiuOrdersModel')->updateOrderStatus($parames);
        if($actionUpdate>0){
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

    /**
     * [OrderListFunction 获取订单列表方法]
     */
    private function OrderListFunction($parames,$page, $pageSize){
        $order_status = json_decode($parames['order_status']);
        $where= array();
        if ($parames['attr']==1) {
            $where['user_id'] = $parames['attr_id'];//用户
        }
        if ($parames['attr']==2) {
            $where['shop_id']=$parames['attr_id'];//商家
        }
        if ($parames['attr']==3) {
            $where['fix_id'] = $parames['attr_id'];//修哥
        }
        if ($parames['attr']==4) {
            $where['agent_id'] = $parames['attr_id'];//代理商
        }
        $where['order_status'] =$order_status;

        $orderList = M_Mysqli_Class('cro','XiuOrdersModel')->getAllFixOrders($where,$page, $pageSize);
        if (empty($orderList )) {
            return $orderList;
        }
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
                // 'shop_id',
                // 'shop_name',
                'agent_id',
                'agent_name',
                'brand_id',
                'brand_name',
                'order_type',
                'order_amount',
                'create_date',
                'booking_date',
                'pay_date',
                'order_status'
            );
        $fixIdList=array_column($orderList, 'fix_id'); //获取修哥id数组集合
        $fixIdList=array_filter($fixIdList);//修哥id数组集合去空值
        if (!empty($fixIdList)) {
            $fixIconList=M_Mysqli_Class('cro', 'ImgModel')->getXgIconList($fixIdList,1,1);
        }else{
            $fixIconList='';
        }
        foreach($orderList as $k=>$v)
        {
            $orderList[$k]=$this->setArray($keyArray, $v);
            if (!empty($v['fix_id'])) 
            {
                    if (!empty($fixIconList)) 
                    {
                        foreach ($fixIconList as $s => $j) 
                        {
                            if ($j['attr_id']==$v['fix_id']) 
                            {
                                $orderList[$k]['xg_icon']=['hash'=>$j['img_hash'],'key'=>$j['img_key']];
                            }else{
                                $orderList[$k]['xg_icon']=['hash'=>'','key'=>''];
                            }
                        }
                    }else{
                        $orderList[$k]['xg_icon']=['hash'=>'','key'=>''];
                    }
            }else{
                    $orderList[$k]['xg_icon']=['hash'=>'','key'=>''];
            }
            $orderList[$k]['user_icon']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$v['user_id'],'attr'=>1,'type'=>1]);
        }
        return $orderList;
    }

    /**
     * [OrderInfoFunction 获取订单详情]
     * @param [type] $parames [description]
     */
    private function OrderInfoFunction($parames){
        $orderInfo = M_Mysqli_Class('cro', 'XiuOrdersModel')->getOrderInfoById($parames['order_id']);
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
                'fix_phone',
                'shop_id',
                'shop_name',
                'agent_id',
                'agent_name',
                'brand_id',
                'brand_name',
                'order_type',
                'order_amount',
                'create_date',
                'booking_date',
                'pay_date',
                'order_status',
                'cancel_reason',
                'fix_location_longitude',
                'fix_location_latitude'
                );
        $return=$this->setArray($keyArray, $orderInfo);
        $serviceInfo = M_Mysqli_Class('cro','XiuServiceModel')->getServerById($orderInfo['service_id']);
        $return['service_type'] = $serviceInfo['service_type'];
        $return['user_icon']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$return['user_id'],'attr'=>1,'type'=>1]);//用户头像
        if (empty($orderInfo['fix_id'])) 
        {
            $return['xg_icon']=['hash'=>'','key'=>''];
            $return['xg_score']='';
            $return['xg_ordernums']='';
            return $return;
        }
        $return['xg_icon']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$return['fix_id'],'attr'=>1,'type'=>1]);//修哥头像
        $fixInfo = M_Mysqli_Class('cro','OrderEvaluationModel')->evaluationAvg(['fix_id'=>$orderInfo['fix_id'],'platform_id'=>2]);//修哥各评价平均值
        $keyArray=array('thy_avg','sve_avg','apd_avg');
        $avgArr=$this->setArray($keyArray, $fixInfo);
        $return['xg_score'] =round(array_sum($avgArr)/count($avgArr),1);//修哥评价平均分
        $return['xg_ordernums'] = M_Mysqli_Class('cro','XiuOrdersModel')->getFixOrder($orderInfo['fix_id'],'3')['num'];//获取修哥已完成订单的数量
        $evaluation = M_Mysqli_Class('cro','OrderEvaluationModel')->getEvaluationInfoByAtt(['platform_id'=>'2','order_id'=>$parames['order_id']]);//订单评价
        $return['evaluation_status'] = count($evaluation)>0 ? '1' : '0';//评价状态

        $orderEltInfo = M_Mysqli_Class('cro','OrderEvaluationModel')->evaluationAvg(['order_id'=>$parames['order_id'],'platform_id'=>2]);//订单评价
        $keyArray=array('thy_avg','sve_avg','apd_avg');
        $avgArray=$this->setArray($keyArray, $orderEltInfo);
        $return['order_score'] =round(array_sum($avgArray)/count($avgArray),1);//订单评价平均分
        return $return;
    }


    private function TimeoutAutoFinishFunction(){
        $result = M_Mysqli_Class('cro','XiuOrdersModel')->getTimeoutOrders();
        return $result;
    }
}
?>
