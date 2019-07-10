<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 * 订单推送
 */
class orderjpush extends MY_Controller {

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
            case "actionPostFixReceiveOrder"://修哥接单
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('id','订单Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('fix_id','修哥ID','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('fix_location','修哥坐标','trim|min_length[1]|max_length[100]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut=$this->PostFixReceiveOrderFunction($parames);
                }
                break;
            case "actionPostFixDepartOrder"://修哥出发
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('id','订单Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('fix_id','修哥ID','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['fix_id'], $parames['token']);
                    $outPut=$this->PostFixDepartOrderFunction($parames);
                }
                break;
            case 'actionPostFixArrive'://修哥到达
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('id','订单Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('fix_id','修哥Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['fix_id'], $parames['token']);
                    $outPut = $this->PostFixArriveFunction($parames);
                }
                break;
            case 'actionPostAccomplishOrder'://订单完成
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('id','订单Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户名','trim|min_length[1]|max_length[50]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $outPut = $this->AccomplishOrderFunction($parames);
                }
                break;
            case 'actionPostCancelOrder'://订单取消
                    $this->form_validation->set_data($parames);
                    $this->form_validation->set_rules('id','订单Id','numeric|min_length[1]|max_length[11]|required');
                    $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                    $this->form_validation->set_rules('token','用户名','trim|min_length[1]|max_length[50]|required');
                    $this->form_validation->set_rules('cancel_reason','取消原因','trim|min_length[1]|required');
                    if ($this->form_validation->run() === FALSE) {
                        $outPut['status']="error";
                        $outPut['code']="1001";
                        $outPut['msg']=$this->form_validation->validation_error();
                        $outPut['data']="";
                    }else{
                        $this->_checkUserLogin($parames['user_id'], $parames['token']);
                        $outPut = $this->CancelOrderFunction($parames);
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



   /*
     * get方法
     */
    public function actionGet(){
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case 'actionGetAppVersion'://获取app版本
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('type','app类型','numeric|min_length[1]|max_length[3]');
                $this->form_validation->set_rules('port_type','应用端类型','numeric|min_length[1]|max_length[3]');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    if (!isset($parames['type']) || empty($parames['type'])) {
                        $parames['type'] = '1';
                    }
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']='';
                    $outPut['data']=$this->GetAppVersionFunction($parames);
                }
                break;
            case 'actionGetPayType'://支付方式配置
                $apiVersion= isset($parames['versionCode']) ? $parames['versionCode'] :0;//获取app版本
                if($apiVersion<102){
                       $paytype = [
                                            ['pay_type'=>'2','type_name'=>'支付宝支付','icon'=>'http://otfl590no.bkt.clouddn.com/alipay.png','describe'=>'推荐有支付宝账户的用户使用'],
                                        ];
                }else{
                      $paytype = [
                                            ['pay_type'=>'2','type_name'=>'支付宝支付','icon'=>'http://otfl590no.bkt.clouddn.com/alipay.png','describe'=>'推荐有支付宝账户的用户使用'],
                                            ['pay_type'=>'1','type_name'=>'微信支付','icon'=>'http://otfl590no.bkt.clouddn.com/weichat.png','describe'=>'推荐安装微信5.0及以上版本的用户使用'],
                                        ];
                }
                
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']='';
                $outPut['data']= $paytype;
                break;
            case 'actionSendVoiceTest':
                $data['mobile']='13816439927';
                $data['content']='您有新的订单，请及时处理';
                $outPut = F()->Voice_message_module->sendVoiceMessage($data);
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
     * 修哥接单
     */
    private function PostFixReceiveOrderFunction($parames){
        unset($parames['action']);
        unset($parames['token']);
        $fixInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['fix_id']]);//修哥用户信息
        $shopInfo = M_Mysqli_Class('cro','ShopModel')->getShopInfoByAttr(['id'=>$fixInfo['parent_id']]);
        $orderInfo = M_Mysqli_Class('cro','XiuOrdersModel')->getOrderInfoById($parames['id']);//订单信息
        $version = isset($parames['version']) ? $parames['version'] : 0;
        $serviceVersion= $this->serviceVersion($orderInfo['service_id'],$version);
        unset($parames['version']);
        if ($serviceVersion) {
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="当前版本太低,请更新版本重试";
            $outPut['data']="";
            return $outPut;exit;
        }
        if ($orderInfo['order_status']!='0') {
            $outPut['status']="error";
            $outPut['code']="3002";
            $outPut['msg']="抢单失败";
            $outPut['data']="";
        }else{
            $fixLocationArray=explode(',', $parames['fix_location']);
            $parames['fix_location_longitude']=$fixLocationArray['0'];
            $parames['fix_location_latitude']=$fixLocationArray['1'];
            $orderUpdate = [
                                    'id'=>$parames['id'],
                                    'fix_id'=>$parames['fix_id'],
                                    'fix_phone' => $fixInfo['username'],
                                    'fix_name'=>$fixInfo['name'],
                                    'shop_id'=> $fixInfo['parent_id'],
                                    'shop_name' =>$shopInfo['name'],
                                    'shop_user_id' => $shopInfo['user_id'],
                                    'order_status' => '1',
                                    'shop_location_longitude' => $shopInfo['lng'],
                                    'shop_location_latitude' => $shopInfo['lat'],
                                    'fix_location_longitude' => $parames['fix_location_longitude'],
                                    'fix_location_latitude' => $parames['fix_location_latitude'],
                                    'receive_time' => time(),
                                    'receive_date' => date('Y-m-d H:i:s'),
                                ];
            M_Mysqli_Class('cro','XiuOrdersModel')->trans_start();//事务开启
            M_Mysqli_Class('cro','XiuOrdersModel')->updateOrderAttr($orderUpdate);//修改订单
            if ($orderInfo['order_type']=='0') {
                M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['order_status'=>'1'],['fix_id'=>$parames['fix_id']]);//修哥接单状态变为已接单
            }
            M_Mysqli_Class('cro','XiuOrdersModel')->trans_complete();
            if(M_Mysqli_Class('cro','XiuOrdersModel')->trans_status() != FALSE){
                $countentArr = array(
                        'type' => '1',
                        'data' => array("orderId"=>$orderInfo['id'],'order_status'=>'1')
                    );
                $countent = json_encode($countentArr);
                $pushReturn = F()->Jpush_module->send($parames['fix_id'],[$orderInfo['user_id']],$countent,array('android'),'user');
                // $data['order']=array('order_id'=>$xiuOrderId);
                $data['pushReturn'] = $pushReturn;
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="抢单成功";
                $outPut['data']=$data;
            }else{
                $outPut['status']="error";
                $outPut['code']="0118";
                $outPut['msg']="网络问题，请重试";
                $outPut['data']="";
            }
        }
        return $outPut;
    }

    //判断服务类型和版本
    private function serviceVersion($service_id,$version)
    {
        $serviceInfo = M_Mysqli_Class('cro','XiuServiceModel')->getServerById($service_id);
        if ($serviceInfo['service_type']==2 && $version<105) {
            return TRUE;
        }else{
            return FALSE;
        }
    }
     /**
      * [PostFixDepartOrderFunction 修哥出发]
      * @param [type] $parames [description]
      */
     private function PostFixDepartOrderFunction($parames)
     {
         unset($parames['action']);
         unset($parames['token']);
         $orderInfo = M_Mysqli_Class('cro','XiuOrdersModel')->getOrderInfoById($parames['id']);//订单信息
         $version = isset($parames['version']) ? $parames['version'] : 0;
         $serviceVersion= $this->serviceVersion($orderInfo['service_id'],$version);
         unset($parames['version']);
         if ($serviceVersion) {
             $outPut['status']="error";
             $outPut['code']="0118";
             $outPut['msg']="当前版本太低,请更新版本重试";
             $outPut['data']="";
             return $outPut;exit;
         }
         if (($orderInfo['fix_id']!=$parames['fix_id'])||$orderInfo['order_status']!=1) {
             $outPut['status']="error";
             $outPut['code']="0118";
             $outPut['msg']="操作失败";
             $outPut['data']="";
         }else{
             $time= time();
             $parames['depart_date']=date("Y-m-d H:i:s",$time);
             $parames['depart_time']=$time;
             $parames['order_status'] = 5;//修哥出发
             $actionUpdate = M_Mysqli_Class('cro', 'XiuOrdersModel')->updateOrderAttr($parames);
             if($actionUpdate){
                 $contentArr=[
                                         'type'=>1,
                                         'data'=>[
                                                         'orderId'=>$orderInfo['id'],
                                                         'order_status'=>5
                                         ]
                 ];
                 $content=json_encode($contentArr);
                 F()->Jpush_module->send($orderInfo['fix_id'],[$orderInfo['user_id']],$content,array('android'),'user');
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
         }
         unset($orderInfo);
         return $outPut;
    }

    /**
     * 保险激活
     */
    public function insureActivate($order_id,$remark)
    {
        $xiucro = M_Mysqli_Class('cro','CroXiuOrderRelativeModel')->getCXRelateiveByAttr(['xiu_order_id'=>$order_id]);//根据修铺订单查询中间表
        $croOrderInfo = M_Mysqli_Class('cro','CroOrdersModel')->getOrderInfoById($xiucro['cro_order_id']);//CRO订单
         $equipment = M_Mysqli_Class('cro','EquipmentModel')->getEquipmentByAttr(['number'=>$remark]);//查询激活码
         if (empty($equipment)) {
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="激活码不存在";
            $outPut['data']="";
            return $outPut;exit;
        }
        if ($equipment['status']!='0') {
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="该激活码已被使用或设备被损坏，请更换激活码";
            $outPut['data']="";
            return $outPut;exit;
        }
         $carinInfo = M_Mysqli_Class('cro','CarInsureModel')->getCarInsuresInfoByIds(['id'=>$croOrderInfo['carinid']]);//carinsure信息

         M_Mysqli_Class('cro','CarInsureModel')->trans_start();

         M_Mysqli_Class('cro','CarInsureModel')->updateCarInsureByAttr(['status'=>'1','status_time'=>time(),'end_time'=>strtotime("+1 year")],['id'=>$croOrderInfo['carinid']]);//启动保险
          M_Mysqli_Class('cro','CarModel')->updateCar(['remark'=>$remark],['id'=>$carinInfo['car_id']]);//添加激活码
          M_Mysqli_Class('cro','ShopModel')->updateBalance(['balance'=>'balance+'.$this->shopCommission,'balance1'=>$this->shopCommission],$croOrderInfo['shopid']);//修改店铺余额
           M_Mysqli_Class('cro','EquipmentModel')->updateEquipment(['status'=>'1','user_id'=>$croOrderInfo['user_id'],'shop_id'=>$croOrderInfo['shopid'],'car_id'=>$carinInfo['car_id'],'order_id'=>$croOrderInfo['id']],['number'=>$remark]);//修改设备表
           if ($croOrderInfo['receiver_status']!='2') {
               M_Mysqli_Class('cro','OrderModel')->updateOrderByAttr(['receiver_status'=>'2','receiver_time'=>time()],['id'=>$croOrderInfo['id']]);//修改订单为确认收货
           }
           M_Mysqli_Class('cro','CarInsureModel')->trans_complete();
           if (M_Mysqli_Class('cro','CarInsureModel')->trans_status() === FALSE) {
                $outPut['status']="error";
                $outPut['code']="0118";
                $outPut['msg']="激活失败";
                $outPut['data']="";
           }else{
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="激活成功";
                $outPut['data']="";
           }
           return $outPut;
    }

    /**
     * 修哥到达
     */
    private function PostFixArriveFunction($parames)
    {
        unset($parames['action']);
        unset($parames['token']);
        $remark = isset($parames['remark']) ? $parames['remark'] : '';
        unset($parames['remark']);
        $orderInfo = M_Mysqli_Class('cro','XiuOrdersModel')->getOrderInfoById($parames['id']);//订单信息
        if ($orderInfo['fix_id']!=$parames['fix_id'] || $orderInfo['order_status']!='5') {
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="操作失败";
            $outPut['data']="";
        }else{
            $serviceInfo = M_Mysqli_Class('cro','XiuServiceModel')->getServerById($orderInfo['service_id']);
            if ($serviceInfo['service_type']==2) {//如果为保险服务订单则需要激活
                if (!empty($remark)) {
                    $activate = $this->insureActivate($parames['id'],$remark);
                    if ($activate['code']!='2000') {
                        return $activate;exit();
                    }
                }else{
                    $outPut['status']="error";
                    $outPut['code']="0118";
                    $outPut['msg']="激活码不能为空";
                    $outPut['data']="";
                    return $outPut;exit();
                }
            }
            $time= time();
            $parames['arrive_date']=date("Y-m-d H:i:s",$time);
            $parames['arrive_time']=$time;
            $parames['order_status'] = '2';
            $actionUpdate = M_Mysqli_Class('cro', 'XiuOrdersModel')->updateOrderByAttr($parames,['id'=>$parames['id']]);
            if($actionUpdate>0){
                $countentArr = array(
                        'type' =>'1',
                        'data' =>array("orderId"=>$orderInfo['id'],'order_status'=>'2')
                    );
                $countent=json_encode($countentArr);
                $pushReturn = F()->Jpush_module->send($orderInfo['fix_id'],[$orderInfo['user_id']],$countent,array('android'),'user');
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
        }
        return $outPut;
    }

    /**
     * 订单分佣配置
     */
    private function commission(){
        $commission = array('fix'=>0.6, 'shop'=>0.3);
        return $commission;
    }
    /**
     * 订单完成
     */
    private function AccomplishOrderFunction($parames){
        $orderInfo = M_Mysqli_Class('cro','XiuOrdersModel')->getOrderInfoById($parames['id']);//订单信息
        if ($orderInfo['user_id']==$parames['user_id'] && $orderInfo['order_status']=='2') {//判断用户为下单用户并且修哥已到达
            $userWallet = M_Mysqli_Class('cro','UserWalletModel')->getWalletByUserId($orderInfo['user_id']);//查询用户钱包
            $time = time();
            $date = date('Y-m-d H:i:s');
            $pay_amount = $orderInfo['order_amount'] - $orderInfo['sale_amount'];//订单实付金额
            $commission = $this->commission();//array('fix'=>0.6, 'shop'=>0.3);//订单分佣配置
            $fixCommission = $pay_amount*$commission['fix'];//修哥提成
            $shopCommission = $pay_amount*$commission['shop'];//店铺提成
            if (($userWallet['balance']+$userWallet['giving_balance'])>$pay_amount) {
                $fixWallet = M_Mysqli_Class('cro','UserWalletModel')->getWalletByUserId($orderInfo['fix_id']);//查询修哥钱包
                $shopWallet = M_Mysqli_Class('cro','ShopModel')->getShopInfoByAttr(['id'=>$orderInfo['shop_id']]);//查询商家钱包
                M_Mysqli_Class('cro','XiuOrdersModel')->trans_start();//事务开启

                $updateOrder = [ 'id' => $orderInfo['id'], 'order_status' => '3', 'pay_amount' => $pay_amount, 'complete_time' => $time, 'complete_date' => $date ];
                M_Mysqli_Class('cro','XiuOrdersModel')->updateOrderAttr($updateOrder);//修改订单

                M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['order_status'=>'0'],['fix_id'=>$orderInfo['fix_id']]);//修哥接单状态变为未接单

                if ($userWallet['balance']>=$pay_amount) {//判断余额是否可扣
                    $surplus = $userWallet['balance']-$pay_amount;//账户余额剩余
                    $giving_balance = $userWallet['giving_balance'];//赠送余额剩余
                    $userWalletEdit = ['balance'=> $surplus];
                }else{
                    $surplus = '0';
                    $giving_balance = $userWallet['giving_balance']-($pay_amount-$userWallet['balance']);
                    $userWalletEdit = [ 'balance'=>$surplus, 'giving_balance'=>$giving_balance];
                }
                M_Mysqli_Class('cro','UserWalletModel')->updateWalletBalance($userWalletEdit,$orderInfo['user_id']);//修改用户余额

                M_Mysqli_Class('cro','UserWalletModel')->updateWalletBalance(['balance'=>'balance+'.$fixCommission,'total_balance'=>'total_balance+'.$fixCommission],$orderInfo['fix_id']);//修改修哥余额

                M_Mysqli_Class('cro','ShopModel')->updateBalance(['balance'=>'balance+'.$shopCommission,'balance1'=>'balance1+'.$shopCommission],$orderInfo['shop_id']);//修改店铺余额

                $userBalance = $this->WalletLogData($orderInfo['user_id'],$userWallet['id'],$pay_amount,$userWallet['balance'],$surplus,'2','6',$orderInfo['id'],$userWallet['giving_balance'],$giving_balance);
                M_Mysqli_Class('cro', 'UserWalletLogModel')->addUserWalletLog($userBalance);//用户钱包日志

                $fixWallet = M_Mysqli_Class('cro','UserWalletModel')->getWalletByUserId($orderInfo['fix_id']);//查询修哥钱包
                $fixBalance = $this->WalletLogData($orderInfo['fix_id'],$fixWallet['id'],$fixCommission,$fixWallet['balance']-$fixCommission,$fixWallet['balance'],'1','3',$orderInfo['id'],$fixWallet['giving_balance'],$fixWallet['giving_balance']);
                M_Mysqli_Class('cro', 'UserWalletLogModel')->addUserWalletLog($fixBalance);//修哥钱包日志

                $shopBalance = $this->WalletLogData($shopWallet['user_id'],'0',$shopCommission,$shopWallet['balance'],$shopWallet['balance']+$shopCommission,'1','3',$orderInfo['id'],'0','0');
                M_Mysqli_Class('cro', 'UserWalletLogModel')->addUserWalletLog($shopBalance);//店铺钱包日志

                M_Mysqli_Class('cro','XiuOrdersModel')->trans_complete();
                if(M_Mysqli_Class('cro','XiuOrdersModel')->trans_status()===FALSE){
                    $outPut['status']="error";
                    $outPut['code']="0118";
                    $outPut['msg']="操作失败";
                    $outPut['data']="";
                }else{
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="操作成功";
                    $outPut['data']="";
                }
            }else{
                $outPut['status']="error";
                $outPut['code']="3004";
                $outPut['msg']="余额不足";
                $outPut['data']="";
            }
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="操作失败";
            $outPut['data']="";
        }
        return $outPut;
    }

    /**
     * [CancelOrderFunction 订单取消方法]
     */
    private function CancelOrderFunction($parames)
    {
        unset($parames['action']);
        unset($parames['token']);
        $orderInfo = M_Mysqli_Class('cro','XiuOrdersModel')->getOrderInfoById($parames['id']);//订单信息
        $order_status = array(2,3,5,4,6);
        if (($orderInfo['user_id']!=$parames['user_id']) || in_array($orderInfo['order_status'], $order_status))
        {
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="取消失败";
            $outPut['data']="";
            return $outPut;
        }
        $time= time();
        $parames['cancel_date']=date("Y-m-d H:i:s",$time);
        $parames['cancel_time']=$time;
        $parames['order_status'] = 4;//订单取消
        M_Mysqli_Class('cro','XiuOrdersModel')->trans_start();//事务开启
        M_Mysqli_Class('cro', 'XiuOrdersModel')->updateOrderAttr($parames);
        if (!empty($orderInfo['fix_id'])) //已接单订单,修改修哥状态,并发送推送消息
        {
            M_Mysqli_Class('cro', 'FixStatusModel')->updateFixStatus(['order_status'=>0],['fix_id'=>$orderInfo['fix_id']]);
            $contentArr=[
                                    'type'=>1,
                                    'data'=>[
                                                    'orderId'=>$orderInfo['id'],
                                                    'order_status'=>4
                                    ]
            ];
            $content=json_encode($contentArr);
             F()->Jpush_module->send($orderInfo['user_id'],[$orderInfo['fix_id']],$content,array('android'),'fix');
        }
        M_Mysqli_Class('cro','XiuOrdersModel')->trans_complete();
        if(M_Mysqli_Class('cro','XiuOrdersModel')->trans_status() != FALSE)
        {
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="操作成功";
            $outPut['data']="";
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="网络问题，请重试";
            $outPut['data']="";
        }
        unset($orderInfo);
        return $outPut;
    }

    //钱包日志数组
    private function WalletLogData($user_id,$wallet_id,$amount,$before_balance,$after_balance,$income_type,$type,$primary_id,$before_giving_balance,$after_giving_balance){
        $data = [
                        'user_id' => $user_id,
                        'wallet_id' => $wallet_id,
                        'amount' => $amount,
                        'before_balance' => $before_balance,
                        'after_balance' => $after_balance,
                        'before_giving_balance'=>$before_giving_balance,
                        'after_giving_balance' =>$after_giving_balance,
                        'income_type' => $income_type,
                        'type' => $type,
                        'primary_id' =>$primary_id
                    ];
        return $data;
    }

    /**
     * app版本
     */
    private function GetAppVersionFunction($parames){
        unset($parames['action']);
        $result = M_Mysqli_Class('cro','VersionModel')->getVersionByAtt($parames);
        // $keyArray=array('id', 'version', 'version_name', 'download_link','describe');
        // $result=$this->setArray($keyArray, $result);
        return $result;
    }
}
?>
