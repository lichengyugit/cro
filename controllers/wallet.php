<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class wallet extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();
    }

    /**
     * Post方法
     */
    public function actionPost(){
        $parames=$this->parames;
            switch ($parames['action'])
            {
                case "actionPostWalletBalance"://获取钱包余额
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('role_flag','角色Id','trim|min_length[1]|max_length[6]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['id'], $parames['token']);
                            $outPut['status']='ok';
                            $outPut['code']="2000";
                            $outPut['msg']='';
                            $outPut['data'] = $this->WalletBalanceFunction($parames);//获取钱包余额
                        }
                        break;
                    case "actionPostApplyWithdraw"://发起申请提现
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('type','提现类型','trim|min_length[1]|max_length[10]|required');
                        $this->form_validation->set_rules('remark','类型描述','trim|min_length[1]|max_length[30]|required');
                        $this->form_validation->set_rules('bankid','提现银行卡Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('money','提现金额','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('platform_type','发起平台','trim|min_length[1]|max_length[6]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $outPut = $this->ApplyWithdrawFunction($parames);
                        }
                        break;
                    case "actionPostBalanceDetail"://获取余额明细
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
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
                            $outPut['data'] = $this->BalanceDetailFunction($parames,$parames['page'], intval($parames['pageSize']));
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
            case "actionGetUserBalanceDetailInfo"://获得用户余额明细详情
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('id','明细Id','numeric|min_length[1]|max_length[11]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->UserBalanceDetailInfoFunction($parames);
                }
                break;
            case 'actionGetPayConfig'://获取支付配置
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->GetPayConfigFunction();
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
     * [WalletBalanceFunction 获取钱包余额的方法]
     **/
    private function WalletBalanceFunction($parames){
        unset($parames['token']);
        unset($parames['action']);
        $userWallet = array();
        if ($parames['role_flag']=='0' || $parames['role_flag']=='3' || $parames['role_flag']=='4') {
            $userWallet = M_Mysqli_Class('cro', 'UserWalletModel')->getWalletByUserId($parames['id']);
        }else if ($parames['role_flag']=='1') {
            $userWallet = M_Mysqli_Class('cro', 'ShopModel')->getShopInfoByAttr(['user_id'=>$parames['id']]);
        }else if ($parames['role_flag']=='2') {
            $userWallet = M_Mysqli_Class('cro', 'ShopModel')->getPartnerInfoByAttr(['user_id'=>$parames['id']]);
        }
        $data['balance'] = count($userWallet)>0 ? $userWallet['balance'] : 0;
        $data['giving_balance'] = count($userWallet)>0 ? $userWallet['giving_balance'] : 0;
        $data['total_balance'] = $data['balance']+$data['giving_balance'];
        return $data;
    }
    /**
     * [ApplyWithdrawFunction 申请提现的方法]
     **/
    private function ApplyWithdrawFunction($parames){
        unset($parames['token']);
        unset($parames['action']);
        $userWallet = M_Mysqli_Class('cro', 'UserWalletModel')->getWalletByUserId($parames['user_id']);
        $parames['surplus_money'] = ($userWallet['balance']+$userWallet['giving_balance'])-$parames['money'];
        $parames['createip'] = $this->getClientIP();
        if ((date('d')<15)||(date('d')>21)) 
        {
             $outPut['status']="error";
            $outPut['code']="2004";
            $outPut['msg']="提现日期范围:每月的15号到21号";
            $outPut['data']="";
            return $outPut;
            exit;
        }

        if ($parames['surplus_money']/100<200) {

            $outPut['status']="error";
            $outPut['code']="2007";
            $outPut['msg']="提现后剩余金额不能小于200元";
            $outPut['data']="";
            return $outPut;
            exit;
        }
        if (($userWallet['balance']+$userWallet['giving_balance'])<$parames['money']) {
            $outPut['status']="error";
            $outPut['code']="2001";
            $outPut['msg']="提现金额超过您的最大提现金额";
            $outPut['data']="";
        }elseif ($userWallet['status']=='1') {
            $outPut['status']="error";
            $outPut['code']="2002";
            $outPut['msg']="您的账户已被冻结，暂时无法提现";
            $outPut['data']="";
        }
        else{
            $userInfo=M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr(['id'=>$parames['user_id']]);
            if (empty($userInfo)) {
               return;
            }
            $userIdList=M_Mysqli_Class('cro', 'UserModel')->getConditionUser(['username'=>$userInfo['username']]);
            $idlist=array_column($userIdList,'id');
            $res= M_Mysqli_Class('cro', 'BankCardModel')->getBankcardListByUids($idlist);
            // $bankList = M_Mysqli_Class('cro', 'BankCardModel')->getBankcardByAttr(['id'=>$parames['bankid'],'user_id'=>$parames['user_id']]);
            if (count($res)>0) {//验证是否本人银行卡
                $return = M_Mysqli_Class('cro', 'TixianModel')->tixiantrans($parames);
                if ( $return['status'] === FALSE)
                {
                    $outPut['status']="error";
                    $outPut['code']="0118";
                    $outPut['msg']="网络问题，请重试";
                    $outPut['data']="";
                }else{
                    if ($userWallet['balance']>=$parames['money']) {
                        $data = [
                                    'user_id' => $parames['user_id'],
                                    'wallet_id' => $userWallet['id'],
                                    'amount' => $parames['money'],
                                    'before_balance' => $userWallet['balance'],
                                    'after_balance' =>  $userWallet['balance']-$parames['money'],
                                    'before_giving_balance' => $userWallet['giving_balance'],
                                    'after_giving_balance' => $userWallet['giving_balance'],
                                    'income_type' => '2',
                                    'type' => '2',
                                    'primary_id' =>$return['primary_id']
                                ];
                    }else{
                        $data = [
                                    'user_id' => $parames['user_id'],
                                    'wallet_id' => $userWallet['id'],
                                    'amount' => $parames['money'],
                                    'before_balance' => $userWallet['balance'],
                                    'after_balance' => 0,
                                    'before_giving_balance' => $userWallet['giving_balance'],
                                    'after_giving_balance' =>  $userWallet['giving_balance']-($parames['money']-$userWallet['balance']),
                                    'income_type' => '2',
                                    'type' => '2',
                                    'primary_id' =>$return['primary_id']
                                ];
                    }

                M_Mysqli_Class('cro', 'UserWalletLogModel')->addUserWalletLog($data);
                    $outPut['status']='ok';
                    $outPut['code']="2000";
                    $outPut['msg']='申请成功';
                    $outPut['data'] = '';
                }
            }else{
                $outPut['status']="error";
                $outPut['code']="2003";
                $outPut['msg']="请选择正确的银行卡";
                $outPut['data']="";
            }
        }
        return $outPut;
    }

    /**
     * [BalanceDetailFunction 获取账户余额明细方法]
     */
    private function BalanceDetailFunction($parames,$page,$pageSize){
        unset($parames['token']);
        unset($parames['action']);
        unset($parames['page']);
        unset($parames['pageSize']);
        $detailList = M_Mysqli_Class('cro', 'UserWalletLogModel')->getAllUserWalletLog($parames,$page, $pageSize);
        $keyArray = ['id','amount','create_date','income_type','type','after_balance','after_giving_balance'];
       foreach($detailList as $k=>$v){
            $detailList[$k]=$this->setArray($keyArray, $v);
            $detailList[$k]['type_name'] = $this->BalanceTypeFunction($v['type']);
            $detailList[$k]['total_balance'] = $v['after_balance']+$v['after_giving_balance'];
        }
        return $detailList;
    }

    private function BalanceTypeFunction($type){
        $type_name = '';
        switch ($type) {
            case '1':
                $type_name = '充值';
                break;
            case '2':
                $type_name = '提现';
                break;
            case '3':
                $type_name = '分佣';
                break;
            case '4':
                $type_name = '赠送';
                break;
            case '5':
                $type_name = '提现退款';
                break;
            case '6':
                $type_name = '下单';
                break;
            case '7':
                $type_name = '新用户赠送';
                break;
            case '8':
                $type_name = '返现';
                break;
            default:
                $type_name = '其他';
                break;
        }
        return $type_name;
    }
    /**
     * [UserBalanceDetailInfoFunction 获取账户余额明细详情方法]
     */
    private function UserBalanceDetailInfoFunction($parames){
        unset($parames['token']);
        unset($parames['action']);
        $WalletLogInfo = M_Mysqli_Class('cro', 'UserWalletLogModel')->getUserWalletLogInfoById($parames['id']);
        $type = $WalletLogInfo['type'];
        $info = array();
        switch ($type) {
            case ($type==1 || $type==4):
                $resultInfo = M_Mysqli_Class('cro', 'TopUpModel')->getTopUpInfoById($WalletLogInfo['primary_id']);
                // $userBalance = M_Mysqli_Class('cro', 'UserWalletModel')->getWalletByUserId($WalletLogInfo['user_id']);
                $info['money'] = $WalletLogInfo['amount'];
                $info['create_date'] = $WalletLogInfo['create_date'];
                $info['after_balance'] = $WalletLogInfo['after_balance'];
                $info['pay_type'] = $resultInfo['pay_type']=='1' ? '微信' : '支付宝';
                $info['status'] = '';
                $info['updare_date'] = '';
                break;
            case ($type==2 || $type==5):
                $resultInfo = M_Mysqli_Class('cro', 'TixianModel')->getTixianInfoByAttr($WalletLogInfo['primary_id']);
                $info['money'] = $resultInfo['money'];
                $info['after_balance'] = $resultInfo['surplus_money'];
                $info['create_date'] = $resultInfo['createdate'];
                $info['status'] = $resultInfo['status'];
                $info['updare_date'] = !empty($resultInfo['updatetime']) ? date('Y-m-d',$resultInfo['updatetime']) : $resultInfo['createdate'];
                break;
            case ($type==3 || $type==6):
                $resultInfo = M_Mysqli_Class('cro', 'XiuOrdersModel')->getOrderInfoById($WalletLogInfo['primary_id']);
                $info['money'] = $WalletLogInfo['amount'];
                $info['create_date'] = $WalletLogInfo['create_date'];
                $info['after_balance'] = $WalletLogInfo['after_balance'];
                $info['service_name'] = $resultInfo['service_name'];
                $info['status'] = '';
                $info['updare_date'] = '';
                break;
            default:
                $info['status'] = '';
                $info['updare_date'] = '';
                $info['money'] = $WalletLogInfo['amount'];
                $info['create_date'] = $WalletLogInfo['create_date'];
                $info['after_balance'] = $WalletLogInfo['after_balance'];
                break;
        }
        $info['type_name'] = $this->BalanceTypeFunction($type);
        return $info;
    }

    /**
     * 获取支付配置
     */
    private function GetPayConfigFunction(){
        $wheres = ['status'=>'1'];
        $result = M_Mysqli_Class('cro', 'TopUpConfigModel')->getAllTopUpConfig($wheres);
        $keyArray=array('id', 'amount', 'giving_amount', 'pay_type');
        foreach($result as $k=>$v){
            $result[$k]=$this->setArray($keyArray, $v);
            $result[$k]['pay_name'] = $v['pay_type']=='1' ? '微信' : '支付宝';
        }
        return $result;
    }
}