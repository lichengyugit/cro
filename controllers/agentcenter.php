<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 * 快修 代理商中心
 */
class agentcenter extends MY_Controller 
{

    public function __construct() 
    {
        parent::__construct();
        $this->parames=$this->getParames();//调用http流方法
    }

    /**
     * get方法 获取代理商信息
     * 
     */
    public function actionGet()
    {
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionGetAgent"://获取代理商信息
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('agent_id','代理商表id agent_id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
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
                    $outPut['data']=$this->agentInfoFunction($parames);
                }
                break;
            case "actionAgentHomePage"://获取代理商端首页信息
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','代理商用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
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
                    $outPut['data']=$this->agentHomePageFunction($parames);
                }
                break;
            case "actionTodayAgentOrders"://获取代理商端首页当日订单
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','代理商用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('agent_id','代理商Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
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
                    $outPut['data']=$this->todayAgentOrdersFunction($parames);
                }
                break;
            case "actionTodayXgTotal"://获取代理商端首页当日修哥总计
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','代理商用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('agent_id','代理商Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
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
                case "actionAgentApply"://代理商完善信息(入驻)
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('user_id','代理商Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('name','代理商名称','trim|required');
                        $this->form_validation->set_rules('user_name','代理商所有人姓名','trim|required');
                        $this->form_validation->set_rules('phone','联系电话','trim|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('province_id','省份id','trim|required');
                        $this->form_validation->set_rules('province_name','省份','trim|required');
                        $this->form_validation->set_rules('city_id','市id','trim|required');
                        $this->form_validation->set_rules('city_name','市','trim|required');
                        $this->form_validation->set_rules('district_id','地区id','trim');
                        $this->form_validation->set_rules('district_name','地区','trim');
                        $this->form_validation->set_rules('address','详细地址','trim|min_length[1]|max_length[100]|required');
                        $this->form_validation->set_rules('charter_img','营业执照图片路径','trim|min_length[1]|required');
                        // $this->form_validation->set_rules('dianpu_img','代理商照片路径','trim|min_length[1]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $this->_checkAgentName($parames['name']);//验证代理商名是否重复
                            $outPut=$this->addAgentFunction($parames);//添加新代理商数据
                        }
                        break;
                    case "actionAgentSearchOrders"://代理商搜索订单
                            $this->form_validation->set_data($parames);
                            $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
                            $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                            $this->form_validation->set_rules('agent_id','代理商Id','numeric|min_length[1]|max_length[11]|required');
                            $this->form_validation->set_rules('search','搜索数据','trim|required');
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
                                $outPut['status']="ok";
                                $outPut['code']="2000";
                                $outPut['msg']="";
                                $outPut['data']=$this->agentSearchOrdersFunction($parames);//
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
     * [actionPut 代理商中心put方法]
     * @return [type] [description]
     */
    public function actionPut()
    {
                $parames=$this->parames;
                switch ($parames['action'])
                {
                    case "actionAgentUpdate"://代理商编辑
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('user_id','代理商用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('agent_id','代理商信息表Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('phone','联系电话','trim|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('province_id','省份id','trim|required');
                        $this->form_validation->set_rules('province_name','省份','trim|required');
                        $this->form_validation->set_rules('city_id','市id','trim|required');
                        $this->form_validation->set_rules('city_name','市','trim|required');
                        $this->form_validation->set_rules('district_id','地区id','trim');
                        $this->form_validation->set_rules('district_name','地区','trim');
                        $this->form_validation->set_rules('address','详细地址','trim|min_length[1]|max_length[100]|required');
                        $this->form_validation->set_rules('charter_img','营业执照图片路径','trim|min_length[1]|required');
                        $this->form_validation->set_rules('dianpu_img','代理商照片路径','trim|min_length[1]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $outPut=$this->updateAllAgentFuntion($parames);
                        }
                        break;
                    case "actionAgentFixStatusUpdate"://代理商休息营业状态
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','代理商Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('agent_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
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
                            $data['id']=$parames['agent_id'];
                            $data['fix_status']=$parames['fix_status'];
                            $outPut=$this->agentFixStatusFunction($data);//修改营业状态
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
     * [agentSearchOrdersFunction 代理商搜索订单]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function agentSearchOrdersFunction($parames)
    {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        $orderList=M_Mysqli_Class('cro','XiuOrdersModel')->searchAgentOrders($parames['agent_id'],$parames['search'],$parames['page'], intval($parames['pageSize']));
        if (empty($orderList)) {
            return;
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
        foreach($orderList as $k=>$v)
        {
            $orderList[$k]=$this->setArray($keyArray, $v);
        }
        return $orderList;

    }
    /**
     * [todayXgTotalFunction 代理商当日每个修哥的总订单量 和总金额]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    public function todayXgTotalFunction($parames)
    {
        //获取代理商下的修哥
        $xgList=M_Mysqli_Class('cro', 'UserModel')->getConditionXg(['parent_id'=>$parames['agent_id'],'role_flag'=>3,'status'=>1],$parames['page'],intval($parames['pageSize']));
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
        $res=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTimeXgTotal(['agent_id'=>$parames['agent_id'],'order_status'=>3],$xg_id_list,$start_time,$end_time);//获取代理商下每个修哥的当日有效营业额和订单量
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
     * [todayAgentOrdersFunction 代理商当日订单]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    public function todayAgentOrdersFunction($parames)
     {
         $start_time=strtotime(date('Y-m-d',time()));
         // $start_time=1502000000;
         $end_time=strtotime('tomorrow');
         // $end_time=1502162299;
         $orderList=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTimeAgentOrderList(['agent_id'=>$parames['agent_id']],$start_time,$end_time,$parames['page'],$parames['pageSize']);
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
     * [agentHomePageFunction 代理商首页]
     * @param [type] $parames [description]
     */
    private function agentHomePageFunction($parames)
    {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        $userInfo = M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr(['id'=>$parames['user_id']]);
        $agentInfo=[];
        $agentInfo['id']='';
        $agentInfo['name']='';
        $agentInfo['dianpu_img']=['hash'=>'','key'=>''];
        $agentInfo['agent_apply_status']='-1';
        $agentInfo['realname_status']=$userInfo['realname_status'];
        $agentInfo['total_order_num']='';
        $agentInfo['total_agent_amount']='';
        $agentInfo['today_order_num']='';
        $agentInfo['today_agent_amount']='';
        $agentInfo['total_order_cancel']='';
        $agentInfo['today_order_cancel']='';
        $agentData = M_Mysqli_Class('cro', 'XiuAgentModel')->getAgentInfoByAttr($parames);
        if (!empty($agentData)) {
            $agentInfo['id']=$agentData['id'];//代理商表id
            $agentInfo['name']=$agentData['name'];
            $agentInfo['agent_apply_status']=$agentData['status'];//入驻状态
            $agentInfo['dianpu_img']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$agentInfo['id'],'attr'=>3,'type'=>4]);//代理商图片
            //修哥数量
            $agentInfo['xg_nums']=M_Mysqli_Class('cro', 'UserModel')->countAgentXg($agentInfo['id']);
            //营业总额
            $agent_total=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTotalAgentOrder($agentInfo['id'],3);//
            $agentInfo['total_order_num']=$agent_total['total_order_num'];//有效订单量
            $agentInfo['total_agent_amount']=$agent_total['total_order_num']*$this->commission['agent'];//有效订单收益
            //总订单取消率
             $total_cancel_orders=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTotalAgentOrder($agentInfo['id'],4)['total_order_num'];//总计已取消订单量
            $total_order_nums=M_Mysqli_Class('cro', 'XiuOrdersModel')->getAgentOrderNums($agentInfo['id']);//总计订单量
             if ($total_order_nums==0) {
                 $agentInfo['total_order_cancel']=0;
             }else{
                 $agentInfo['total_order_cancel']=round($total_cancel_orders/$total_order_nums,2);
             }
            //当日营业总额
            $start_time=strtotime(date('Y-m-d',time()));
            $end_time=strtotime('tomorrow');
            $today_Agent_total=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTodayAgentOrder($agentInfo['id'],3,$start_time,$end_time);//当日有效订单
            $today_cancel_orders=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTodayAgentCancelOrder($agentInfo['id'],4,$start_time,$end_time)['today_order_num'];//当日取消订单量
            $today_order_nums=M_Mysqli_Class('cro', 'XiuOrdersModel')->getTodayAgentOrderNum($agentInfo['id'],$start_time,$end_time);//当日总订单量
            $agentInfo['today_order_num']=$today_Agent_total['today_order_num'];
            if ($today_order_nums==0) {
                $agentInfo['today_order_cancel']=0;
            }else{
                $agentInfo['today_order_cancel']=round($today_cancel_orders/$today_order_nums,2);
            }
            $agentInfo['today_agent_amount']=$agentInfo['today_order_num']*$this->commission['agent'];
        }
        unset($agentData);//销毁无用数据
        return $agentInfo;
    }

    /**
     * [agentInfoFunction 获取单条代理商信息方法]
     * @param [type] $parames [description]
     */
    public function agentInfoFunction($parames)
    {
        unset($parames['token']);//去除多余数据
        $parames['id']=$parames['agent_id'];
        unset($parames['agent_id']);
        unset($parames['user_id']);
        unset($parames['action']);
        $agentInfo = M_Mysqli_Class('cro', 'XiuAgentModel')->getAgentInfoByAttr($parames);//通过代理商所属用户id获取代理商基本信息
        $keyArray=array("id","user_id","name","user_name","phone","address","province_id","province_name","city_id","city_name","district_id","district_name","status");//代理商基本信息
        $agentInfo=$this->setArray($keyArray,$agentInfo);
        $agentInfo['dianpu_img']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$agentInfo['id'],'attr'=>3,'type'=>4]);//代理商图片
        $agentInfo['charter_img']=M_Mysqli_Class('cro', 'ImgModel')->getImgByAttr(['attr_id'=>$agentInfo['id'],'attr'=>3,'type'=>5]);//营业执照图
        return $agentInfo;
    }

    /**
     * [_checkAgentName 验证代理商名]
     * @return [type] [description]
     */
    private function _checkAgentName($Agentname)
    {
         $row=M_Mysqli_Class('cro', 'XiuAgentModel')->checkAgentName($Agentname);
         if($row>0){
             $outPut['status'] = 'error';
             $outPut['code']="1700";
             $outPut['msg'] = '该代理商已存在,请更换代理商名';
             $outPut['data'] = '';
             $this->setOutPut($outPut);
         }else{
            return true;
         }
    }

    /**
     * [addAgentFunction 添加代理商]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function addAgentFunction($parames)
    {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        // $dianpu_img=json_decode($parames['dianpu_img'],true);
        $charter_img=json_decode($parames['charter_img'],true);
        // unset($parames['dianpu_img']);//去除多余数据
        unset($parames['charter_img']);//去除多余数据
        $parames['create_time']=time();
        $parames['create_date']=date('Y-m-d,H:i:s',$parames['create_time']);
        $parames['create_ip']= $this->getClientIP();
        M_Mysqli_Class('cro', 'XiuAgentModel')->trans_start();
        $newAgentId=M_Mysqli_Class('cro', 'XiuAgentModel')->addAgent($parames);//代理商数据
        $imgData=[];
        // $imgData[0]['attr_id']=$newAgentId;//用户id
        // $imgData[0]['attr']=3;//代理商
        // $imgData[0]['type']=4;//代理商照片
        // $imgData[0]['img_key']=$dianpu_img['key'];//图片key
        // $imgData[0]['img_hash']=$dianpu_img['hash'];
        // $imgData[0]['create_time']=time();
        // $imgData[0]['create_date']=date('Y-m-d,H:i:s',time());
        $imgData[0]['attr_id']=$newAgentId;//代理商id
        $imgData[0]['attr']=3;//代理商
        $imgData[0]['type']=5;//营业执照
        $imgData[0]['img_key']=$charter_img['key'];
        $imgData[0]['img_hash']=$charter_img['hash'];
        $imgData[0]['create_time']=time();
        $imgData[0]['create_date']=date('Y-m-d,H:i:s',time());
        M_Mysqli_Class('cro', 'ImgModel')->addImgPatch($imgData);//图片数据
        M_Mysqli_Class('cro', 'XiuAgentModel')->trans_complete();
        $trans_status=M_Mysqli_Class('cro', 'XiuAgentModel')->trans_status();
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
        return $outPut;
    }


   /**
    * [updateAllAgentFuntion 更新代理商信息]
    * @return [type]          [description]
    */
   public function updateAllAgentFuntion($parames)
   {
        unset($parames['token']);//去除多余数据
        unset($parames['action']);//去除多余数据
        $keyArray=array("phone","province_id","province_name","city_id","city_name","district_id","district_name","address");
        $data=$this->setArray($keyArray,$parames);//过滤数据 组合代理商表更新数据
        $data['id']=$parames['agent_id'];//
        $data['update_time']=time();//更新时间
        $data['update_date']=date('Y-m-d,H:i:s',$data['update_time']);
        $data['update_ip']=$this->getClientIP();//更新ip
        $charter_img=json_decode($parames['charter_img'],true);//代理商营业执照图片数据转换格式
        $dianpu_img=json_decode($parames['dianpu_img'],true);//代理商门头图片数据转换格式
        //代理商图片数组 
        $dianpu_imgData=[];
        $dianpu_imgData['attr_id']=$parames['agent_id'];//代理商id
        $dianpu_imgData['attr']=3;//代理商
        $dianpu_imgData['type']=4;//代理商图
        $dianpu_imgData['img_key']=$dianpu_img['key'];//图片key
        $dianpu_imgData['img_hash']=$dianpu_img['hash'];
        //代理商营业执照数组
        $charter_imgData=[];
        $charter_imgData['attr_id']=$parames['agent_id'];//代理商id
        $charter_imgData['attr']=3;//代理商
        $charter_imgData['type']=5;//营业执照
        $charter_imgData['img_key']=$charter_img['key'];//图片key
        $charter_imgData['img_hash']=$charter_img['hash'];
        $charter_imgOldData=M_Mysqli_Class('cro', 'ImgModel')->getImgInfoByAttr(['attr_id'=>$parames['agent_id'],'attr'=>3,'type'=>5]);//代理商营业执照旧
        $charter_imgData['id']=$charter_imgOldData['id'];
        M_Mysqli_Class('cro', 'XiuAgentModel')->trans_start();
        M_Mysqli_Class('cro', 'XiuAgentModel')->updateAgent($data);
        $dianpu_imgOldData=M_Mysqli_Class('cro', 'ImgModel')->getImgInfoByAttr(['attr_id'=>$parames['agent_id'],'attr'=>3,'type'=>4]);//代理商门头照片旧
        if (!empty($dianpu_imgOldData)) 
        {
            $dianpu_imgData['id']=$dianpu_imgOldData['id'];
            M_Mysqli_Class('cro', 'ImgModel')->updateImg($dianpu_imgData);
        }else{
            M_Mysqli_Class('cro', 'ImgModel')->addImg($dianpu_imgData);
        }
        M_Mysqli_Class('cro', 'ImgModel')->updateImg($charter_imgData);
        M_Mysqli_Class('cro', 'XiuAgentModel')->trans_complete();
        $trans_status=M_Mysqli_Class('cro', 'XiuAgentModel')->trans_status();
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
        unset($data);
        unset($dianpu_imgData);
        unset($charter_imgData);
        return $outPut;
   }


    /**
     * [agentFixStatusFunction 更新代理商信息]
     * @param  [type] $data [description]
     * @return [type]          [description]
     */
    private function agentFixStatusFunction($data)
    {
        $data['update_time']=time();
        $data['update_ip']= $this->getClientIP();
        $res=M_Mysqli_Class('cro', 'XiuAgentModel')->updateAgent($data);
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