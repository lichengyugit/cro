<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class fixagent extends MY_Controller {

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
                    // $this->_checkUserLogin($parames['fix_id'], $parames['token']);
                    $outPut=$this->PostFixReceiveOrderFunction($parames);
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
            case "actionPostFixBindingAgent"://修哥绑定代理商
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('agent_id','代理商ID','numeric|min_length[1]|max_length[11]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $outPut=$this->FixBindingAgentFunction($parames);
                }
                break;
            case "actionPostSearchAgent"://搜索代理商
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('name','搜索条件','required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->SearchAgentFunction($parames);
                }
                break;
            case "actionPostFixAgent"://获取修哥所在代理商
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
                    $outPut['data']=$this->FixAgentFunction($parames);
                }
                break;
            case 'actionPostFixStatus'://修哥操作工作状态
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('fix_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('status','操作状态','numeric|min_length[1]|max_length[5]|required');
                 if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['fix_id'], $parames['token']);
                    $outPut = $this->FixStatusFunction($parames);
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
     * get方法
     */
    public function actionGet(){
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case 'actionGetBindingMessage'://获取商家修哥之间绑定申请列表
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('fix_id','修哥Id','numeric|min_length[1]|max_length[11]');
                $this->form_validation->set_rules('agent_id','店铺ID','trim|min_length[1]|max_length[11]');
                $this->form_validation->set_rules('type','发起类型','trim|min_length[1]|max_length[3]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']='';
                    $outPut['data']=$this->GetBindingMessageFunction($parames);
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
     * [actionPut put方法]
     * @return [type] [description]
     */
    public function actionPut()
    {
                $parames=$this->parames;
                switch ($parames['action'])
                {
                    case "actionXgImpro"://修哥完善信息
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('province_id','省份id','trim|min_length[1]|required');
                        $this->form_validation->set_rules('province_name','省份','trim|min_length[1]|required');
                        $this->form_validation->set_rules('city_id','市id','trim|min_length[1]|required');
                        $this->form_validation->set_rules('city_name','市','trim|min_length[1]|required');
                        $this->form_validation->set_rules('district_id','地区id','trim');
                        $this->form_validation->set_rules('district_name','地区','trim');
                        $this->form_validation->set_rules('agent_id','代理商id','trim|min_length[1]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $outPut=$this->xgImproFunction($parames);
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
     * 订单分佣配置
     * 订单分佣配置(单位：分)
     */
    // private function commission(){
    //     $commission = array('fix'=>600, 'agent'=>290);
    //     return $commission;
    // }

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
     * 订单完成
     */
    private function AccomplishOrderFunction($parames){
        $orderInfo = M_Mysqli_Class('cro','XiuOrdersModel')->getOrderInfoById($parames['id']);//订单信息

        if ($orderInfo['user_id']==$parames['user_id'] && ($orderInfo['order_status']=='2' || $orderInfo['order_status']=='6')) {//判断用户为下单用户并且修哥已到达
            $userWallet = M_Mysqli_Class('cro','UserWalletModel')->getWalletByUserId($orderInfo['user_id']);//查询用户钱包
            $time = time();
            $date = date('Y-m-d H:i:s');
            $pay_amount = $orderInfo['order_amount'] - $orderInfo['sale_amount'];//订单实付金额
            $serviceInfo = M_Mysqli_Class('cro','XiuServiceModel')->getServerById($orderInfo['service_id']);
            if ($serviceInfo['service_type']!=2) {
                $commission = $this->commission;//订单分佣配置
            }else{
                $commission = $this->activateCommission;//上门激活订单分佣配置
            }
            $fixCommission = $commission['fix'];//修哥提成
            $agentCommission = $commission['agent'];//店铺提成
            if (($userWallet['balance']+$userWallet['giving_balance'])>=$pay_amount) {//判断用户钱包余额是否可扣
                M_Mysqli_Class('cro','XiuOrdersModel')->trans_start();//事务开启

                if ($orderInfo['order_status']=='6') {
                    M_Mysqli_Class('cro','OrderAppealModel')->updateOrderAppealByAttr(['appeal_status'=>'1'],['order_id'=>$parames['id']]);
                }

                $updateOrder = [ 'id' => $orderInfo['id'], 'order_status' => '3', 'pay_amount' => $pay_amount, 'complete_time' => $time, 'complete_date' => $date ];
                M_Mysqli_Class('cro','XiuOrdersModel')->updateOrderAttr($updateOrder);//修改订单

                M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['order_status'=>'0'],['fix_id'=>$orderInfo['fix_id']]);//修哥接单状态变为未接单

                if ($userWallet['balance']>=$pay_amount) {//判断账户充值余额余额是否可扣
                    $surplus = $userWallet['balance']-$pay_amount;//账户余额剩余
                    $giving_balance = $userWallet['giving_balance'];//赠送余额剩余
                    $userWalletEdit = ['balance'=> $surplus];
                     M_Mysqli_Class('cro','UserWalletModel')->actionWalletBalance(['balance'=> $pay_amount],$orderInfo['user_id'],'-');//修改用户余额
                }else{
                    $surplus = '0';
                    $giving_balance = $userWallet['giving_balance']-($pay_amount-$userWallet['balance']);
                    $userWalletEdit = [ 'balance'=>$surplus, 'giving_balance'=>$giving_balance];
                    M_Mysqli_Class('cro','UserWalletModel')->actionWalletBalance(['balance'=> '0', 'giving_balance' => ($pay_amount-$userWallet['balance'])],$orderInfo['user_id'],'-');
                }

                M_Mysqli_Class('cro','UserWalletModel')->actionWalletBalance(['balance'=>$fixCommission,'total_balance'=>$fixCommission],$orderInfo['fix_id'],'+');//修改修哥余额

                M_Mysqli_Class('cro','UserWalletModel')->actionWalletBalance(['balance'=>$agentCommission,'total_balance'=>$agentCommission],$orderInfo['agent_user_id'],'+');//修改代理商余额

                M_Mysqli_Class('cro','XiuOrdersModel')->trans_complete();

                $userBalance = $this->WalletLogData($orderInfo['user_id'],$userWallet['id'],$pay_amount,$userWallet['balance'],$surplus,'2','6',$orderInfo['id'],$userWallet['giving_balance'],$giving_balance);

                M_Mysqli_Class('cro', 'UserWalletLogModel')->addUserWalletLog($userBalance);//用户钱包日志

                $fixWallet = M_Mysqli_Class('cro','UserWalletModel')->getWalletByUserId($orderInfo['fix_id']);//查询修哥钱包
                $fixBalance = $this->WalletLogData($orderInfo['fix_id'],$fixWallet['id'],$fixCommission,$fixWallet['balance']-$fixCommission,$fixWallet['balance'],'1','3',$orderInfo['id'],$fixWallet['giving_balance'],$fixWallet['giving_balance']);
                M_Mysqli_Class('cro', 'UserWalletLogModel')->addUserWalletLog($fixBalance);//修哥钱包日志

                $agentWallet = M_Mysqli_Class('cro','UserWalletModel')->getWalletByUserId($orderInfo['agent_user_id']);//查询代理商钱包
                $agentBalance = $this->WalletLogData($agentWallet['user_id'],$agentWallet['id'],$agentCommission,$agentWallet['balance']-$agentCommission,$agentWallet['balance'],'1','3',$orderInfo['id'],$agentWallet['giving_balance'],$agentWallet['giving_balance']);
                M_Mysqli_Class('cro', 'UserWalletLogModel')->addUserWalletLog($agentBalance);//代理商钱包日志
                // M_Mysqli_Class('cro','XiuOrdersModel')->trans_complete();
                if(M_Mysqli_Class('cro','XiuOrdersModel')->trans_status()===FALSE){
                    $outPut['status']="error";
                    $outPut['code']="0118";
                    $outPut['msg']="操作失败";
                    $outPut['data']="";
                }else{
                    $countentArr = array(
                        'type' => '3',
                        'data' => array("orderId"=>$orderInfo['id'],'order_status'=>'3')
                    );
                    $countent = json_encode($countentArr);
                    $pushReturn = F()->Jpush_module->send($orderInfo['user_id'],[$orderInfo['fix_id']],$countent,array('android'),'fix');
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
     * [xgImproFunction 完善修哥信息 完善区域和父级代理商]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
     private function xgImproFunction($parames)
     {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        $userInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['user_id']]);
        if ($userInfo['realname_status']=='0') {
            $outPut['status']="error";
            $outPut['code']="1003";
            $outPut['msg']="请先实名认证";
            $outPut['data']="";
        }else if ($userInfo['status']=='0') {
            $outPut['status']="error";
            $outPut['code']="1004";
            $outPut['msg']="平台审核通过才可绑定，请耐心等待";
            $outPut['data']="";
        }else if($userInfo['status']=='1'){
            $outPut['status']="error";
            $outPut['code']="1005";
            $outPut['msg']="您已绑定成功，请勿重复绑定";
            $outPut['data']="";
        }else if ($userInfo['status']=='4') {
            $outPut['status']="error";
            $outPut['code']="1006";
            $outPut['msg']="您已提交过绑定申请，请耐心等待代理商同意";
            $outPut['data']="";
        }else if ($userInfo['status']=='3') {
            $fixShopData['fix_id'] = $parames['user_id'];
            $fixShopData['agent_id'] = $parames['agent_id'];
            $fixShopData['type'] = 1;
            $parames['id'] = $parames['user_id'];
            $parames['parent_id'] = $parames['agent_id'];
            $parames['status'] = 4;
            unset($parames['user_id']);
            unset($parames['agent_id']);
            M_Mysqli_Class('cro','FixShopModel')->trans_start();
            M_Mysqli_Class('cro','FixShopModel')->addFixShop($fixShopData);
            M_Mysqli_Class('cro','UserModel')->updateUser($parames);
            M_Mysqli_Class('cro','FixShopModel')->trans_complete();
            if (M_Mysqli_Class('cro','FixShopModel')->trans_status() === FALSE) {
                $outPut['status']="error";
                $outPut['code']="0118";
                $outPut['msg']="网络问题，请重试";
                $outPut['data']="1";
            }else{
                $outPut['status']="ok";
                $outPut['msg']="完善成功，请等待代理商同意";
                $outPut['code']="2000";
                $outPut['data']='';
            }
        }else{
            $outPut['status']="error";
            $outPut['code']="1008";
            $outPut['msg']="您的账号目前暂不可绑定代理商";
            $outPut['data']="";
        }
        return $outPut;
     }

    /**
     * [FixBindingShopFunction 修哥绑定代理商方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function FixBindingAgentFunction($parames){
        unset($parames['action']);
        unset($parames['token']);
        $userInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['user_id']]);
        if ($userInfo['realname_status']=='0') {
            $outPut['status']="error";
            $outPut['code']="1003";
            $outPut['msg']="请先实名认证";
            $outPut['data']="";
        }else if ($userInfo['status']=='0') {
            $outPut['status']="error";
            $outPut['code']="1004";
            $outPut['msg']="平台审核通过才可绑定，请耐心等待";
            $outPut['data']="";
        }else if($userInfo['status']=='1'){
            $outPut['status']="error";
            $outPut['code']="1005";
            $outPut['msg']="您已绑定成功，请勿重复绑定";
            $outPut['data']="";
        }else if ($userInfo['status']=='4') {
            $outPut['status']="error";
            $outPut['code']="1006";
            $outPut['msg']="您已提交过绑定申请，请耐心等待代理商同意";
            $outPut['data']="";
        }else if ($userInfo['status']=='3') {
            $agentInfo = M_Mysqli_Class('cro','XiuAgentModel')->getAgentInfoByAttr(['id'=>$parames['agent_id']]);
            if (empty($agentInfo)) {
                $outPut['status']="error";
                $outPut['code']="1007";
                $outPut['msg']="选择的代理商不存在";
                $outPut['data']="";
            }
            M_Mysqli_Class('cro','FixShopModel')->trans_start();
            $parames['fix_id'] = $parames['user_id'];
            $parames['type'] = '1';
            $data['id'] = $parames['user_id'];
            $data['parent_id'] = $parames['agent_id'];
            $data['status'] = '4';
            unset($parames['user_id']);
            M_Mysqli_Class('cro','FixShopModel')->addFixShop($parames);
            $result = M_Mysqli_Class('cro','UserModel')->updateUser($data);
            M_Mysqli_Class('cro','FixShopModel')->trans_complete();
            if (M_Mysqli_Class('cro','FixShopModel')->trans_status() === FALSE) {
                $outPut['status']="error";
                $outPut['code']="0118";
                $outPut['msg']="网络问题，请重试";
                $outPut['data']="1";
            }else{
                $outPut['status']="ok";
                $outPut['msg']="申请成功，请等待代理商同意";
                $outPut['code']="2000";
                $outPut['data']='';
            }
        }else{
            $outPut['status']="error";
            $outPut['code']="1008";
            $outPut['msg']="您的账号目前暂不可绑定代理商";
            $outPut['data']="";
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
     * 修哥接单
     */
    private function PostFixReceiveOrderFunction($parames){
        unset($parames['action']);
        unset($parames['token']);
        $fixInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['fix_id']]);//修哥用户信息
        $agentInfo = M_Mysqli_Class('cro','XiuAgentModel')->getAgentInfoByAttr(['id'=>$fixInfo['parent_id']]);
        $fixStatus=M_Mysqli_Class('cro','FixStatusModel')->getFixInfoByFixId($parames['fix_id']);//修哥状态信息
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
        if ($fixStatus['order_status']=='1' && $orderInfo['order_type']=='0') {//如果修哥已接单并且订单为实时订单
            $outPut['status']="error";
            $outPut['code']="3002";
            $outPut['msg']="抢单失败";
            $outPut['data']="";
            return $outPut;
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
                                    // 'id'=>$parames['id'],
                                    'fix_id'=>$parames['fix_id'],
                                    'fix_phone' => $fixInfo['username'],
                                    'fix_name'=>$fixInfo['name'],
                                    'agent_id'=> $fixInfo['parent_id'],
                                    'agent_name' =>$agentInfo['name'],
                                    'agent_user_id' => $agentInfo['user_id'],
                                    'order_status' => '1',
                                    'fix_location_longitude' => $parames['fix_location_longitude'],
                                    'fix_location_latitude' => $parames['fix_location_latitude'],
                                    'receive_time' => time(),
                                    'receive_date' => date('Y-m-d H:i:s'),
                                ];
            M_Mysqli_Class('cro','XiuOrdersModel')->trans_start();//事务开启
            M_Mysqli_Class('cro','XiuOrdersModel')->updateOrderAttrs($orderUpdate,['id'=>$parames['id'],'order_status'=>'0']);//修改订单
            if ($orderInfo['order_type']=='0') {
                M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['order_status'=>'1'],['fix_id'=>$parames['fix_id'],'order_status'=>'0']);//修哥接单状态变为已接单
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

    /**
     * [GetBindingMessageFunction 获取商家修哥之间绑定申请列表]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function GetBindingMessageFunction($parames){
        unset($parames['action']);
        unset($parames['token']);
        $parames['status'] = '0';
        $fixShopResult = M_Mysqli_Class('cro','FixShopModel')->getAllFixShop($parames);
        $return = array();
        if (!empty($fixShopResult)) {
            $fixIds = array_column($fixShopResult, 'fix_id');
            $agentIds = array_column($fixShopResult, 'agent_id');
            $userResult = M_Mysqli_Class('cro','UserModel')->getUsersInfoByAttr(['id'=>$fixIds]);
            $agentResult = M_Mysqli_Class('cro','XiuAgentModel')->getAgentsInfoByAttr(['id'=>$agentIds]);
            foreach ($fixShopResult as $key => $value) {
                $return[$key]['id'] = $fixShopResult[$key]['id'];
                $return[$key]['fix_id'] = $fixShopResult[$key]['fix_id'];
                $return[$key]['agent_id'] = $fixShopResult[$key]['agent_id'];
                $return[$key]['create_date'] = $fixShopResult[$key]['create_date'];
                foreach ($userResult as $k => $v) {
                    if ($fixShopResult[$key]['fix_id']==$userResult[$k]['id']) {
                        $return[$key]['user_name'] = $userResult[$k]['name'];
                        $return[$key]['fix_phone'] = $userResult[$k]['username'];
                        $iconData=M_Mysqli_Class('cro', 'ImgModel')->getImgInfoByAttr(['attr_id'=>$userResult[$k]['id'],'attr'=>1,'type'=>1]);//头像 
                        $return[$key]['user_img'] =!empty($iconData) ? ["hash"=>$iconData['img_hash'],"key"=>$iconData['img_key']] : ["hash"=>'',"key"=>CRO_IMG.$userResult[$k]['icon']];
                    }
                }
                foreach ($agentResult as $i => $j) {
                    if ($fixShopResult[$key]['agent_id']==$agentResult[$i]['id']) {
                        $return[$key]['agent_name'] = $agentResult[$i]['name'];
                        $return[$key]['agent_address'] = $agentResult[$i]['address'];
                        $return[$key]['agent_img'] =M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$j['user_id'],'attr'=>3,'type'=>4]);
                    }
                }
            }
        }
        return $return;
    }

    /**
     * [SearchShop 搜索代理商方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function SearchAgentFunction($parames)
    {
        $shopList=M_Mysqli_Class('cro', 'XiuAgentModel')->getSearchAgent('1',$parames['name']);
        if(is_array($shopList)){
            $keyArray=array(
                'id',
                'user_id',
                'name',
                'user_name',
                'province_id',
                'province_name',
                'city_id',
                'city_name',
                'district_id',
                'district_name',
                'lng',
                'lat'
            );
            foreach($shopList as $k=>$v){
                $shopList[$k]=$this->setArray($keyArray, $v);
                $shopList[$k]['agent_img'] =M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$v['user_id'],'attr'=>3,'type'=>4]);
            }
        }
        return $shopList;
    }

    /**
     * [FixShopFunction 获取修哥所属店铺方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function FixAgentFunction($parames){
        unset($parames['action']);
        unset($parames['token']);
        $parames['status'] = '1';
        $userInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr($parames);
        $keyArray=array('parent_id');
        $agent_id=$this->setArray($keyArray, $userInfo)['parent_id'];//修哥所属代理商ID
        $agentInfo = M_Mysqli_Class('cro','XiuAgentModel')->getAgentInfoByAttr(['id'=>$agent_id]);
        if (is_array($agentInfo)) {
            $keyArr=array(
                'id',
                'user_id',
                'name',
                'user_name',
                'province_id',
                'province_name',
                'city_id',
                'city_name',
                'district_id',
                'district_name'
            );
            $agentInfo=$this->setArray($keyArr, $agentInfo);
            $agentInfo['agent_img'] =M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$agentInfo['user_id'],'attr'=>3,'type'=>4]);
        }
        return $agentInfo;
    }

    /**
     * [FixStatusFunction 操作修哥状态方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function FixStatusFunction($parames){
        $mem = new Memcache();
        $mem->addserver('127.0.0.1',11211);
        $val=$mem->get($parames['fix_id']);
        if(time()<$val){
            $outPut['status']="error";
            $outPut['code']="1001";
            $outPut['msg']="请勿频繁操作";
            $outPut['data']="";
        }else{
            $mem->set($parames['fix_id'], time()+$this->fixStstusTime);
            // $val=$mem->get('test');
            $userInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['fix_id']]);
            if ($userInfo['status']=='1') {
                switch ($parames['status']) {
                    case '0'://修哥收工
                        $num = M_Mysqli_Class('cro','XiuOrdersModel')->getFixOrder($parames['fix_id'],[1,2,5])['num'];
                        if ($num>0) {
                            $outPut['status']="error";
                            $outPut['code']="1009";
                            $outPut['msg']="还有订单未完成，暂时无法收工";
                            $outPut['data']="";
                        }else{
                            $result = M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['status'=>$parames['status'],'agent_id'=>$userInfo['parent_id']],['fix_id'=>$parames['fix_id']]);
                            if($result>0){
                                $outPut['status']="ok";
                                $outPut['code']="2000";
                                $outPut['msg']="修改成功";
                                $outPut['data']='';
                            }else{
                                $outPut['status']="error";
                                $outPut['code']="0118";
                                $outPut['msg']="网络问题，请重试";
                                $outPut['data']="";
                            }
                        }
                        break;
                    case '1'://修哥开工
                        $result = 0;
                        $fixStatus = M_Mysqli_Class('cro','FixStatusModel')->getFixStatusByFixId($parames['fix_id']);
                        if ($fixStatus['num']>0) {//判断修哥是否有临时信息
                            $result = M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['status'=>$parames['status'],'agent_id'=>$userInfo['parent_id']],['fix_id'=>$parames['fix_id']]);
                        }else{
                            $data = array('fix_id' =>$parames['fix_id'] , 'status'=>'1','order_status'=>'0','agent_id'=>$userInfo['parent_id']);
                            $result = M_Mysqli_Class('cro','FixStatusModel')->addFixStatus($data);
                        }
                        if($result>0){
                            $outPut['status']="ok";
                            $outPut['code']="2000";
                            $outPut['msg']="修改成功";
                            $outPut['data']='';
                        }else{
                            $outPut['status']="error";
                            $outPut['code']="0118";
                            $outPut['msg']="网络问题，请重新操作";
                            $outPut['data']="";
                        }
                        break;
                    default:
                        $outPut['status']="error";
                        $outPut['code']="1020";
                        $outPut['msg']="请正确操作";
                        $outPut['data']="";
                        break;
                }
            }else{
                $outPut['status']="error";
                $outPut['code']="1030";
                $outPut['msg']="对不起，您暂时无法出工,请先绑定代理商";
                $outPut['data']="";
            }
        }
        return $outPut;
    }
}
?>
