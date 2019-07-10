<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 *  代理商修哥控制器
 */
class agentxg extends MY_Controller {

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
            case "actionGetEmployees"://获取我的员工
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
                        $outPut['data']=$this->employeesFunction($parames);
                    }
                    break;
            case "actionGetXubikeAgent"://获取快修自营信息
                    $this->form_validation->set_data($parames);
                    $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                    $this->form_validation->set_rules('token','token','trim|min_length[1]|max_length[50]|required');
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
                        // $outPut['data']=$this->xiubikeAgent;
                        $outPut['data']=$this->getXiubikeAgent();
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
                case "actionSearchEmployees"://搜索修哥
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','代理商用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('search_data','搜索信息','trim|min_length[1]|max_length[50]|required');
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
                            $outPut['data']=$this->searchEmployeesFunction($parames);
                        }
                        break;
                case "actionApplyBindXg"://代理商发起绑定修哥方法
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','代理商Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('agent_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('xg_id','修哥Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $outPut=$this->applyBindXgFuntion($parames);//绑定修哥
                        }
                        break;
                case "actionBindXg"://绑定修哥
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('agent_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('xg_id','修哥Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('msg_id','申请消息Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $condition=[];
                            $condition['id']=$parames['xg_id'];//修哥id
                            $condition['parent_id']=$parames['agent_id'];//商铺id
                            $condition['msg_id']=$parames['msg_id'];//申请消息Id
                            $condition['status']=1;//状态改为1 正常
                            $outPut=$this->bindXgFuntion($condition);//绑定修哥
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
                    case "actionUnbindXg"://代理商拒绝修哥绑定
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','代理商Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('agent_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('xg_id','修哥Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $condition=[];
                            $condition['id']=$parames['xg_id'];//修哥id
                            $condition['parent_id']=0;//父级商铺id变为空
                            $condition['status']=3;//状态改为3 待代理商绑定
                            $outPut=$this->refuseXgFuntion($condition,$parames['agent_id']);//拒绝修哥
                        }
                        break;
                    case "actionDeleteXg"://解绑修哥
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','代理商Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','代理商token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('xg_id','修哥Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('agent_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $condition=[];
                            $condition['id']=$parames['xg_id'];//修哥id
                            $condition['parent_id']=0;//父级商铺id变为空
                            $condition['status']=3;//状态改为3 待代理商绑定
                            $outPut=$this->unbindXgFuntion($condition,$parames['agent_id']);//解绑修哥
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
     * [actionDelete 代理商中心put方法]
     * @return [type] [description]
     */
    public function actionDelete()
    {
                $parames=$this->parames;
                switch ($parames['action'])
                {
                    
                    default:
                        $outPut['status']="error";
                        $outPut['code']="4040";
                        $outPut['msg']="请求错误";
                        $outPut['data']="";
                }
                $this->setOutPut($outPut);
    }

    /**
     * [getXiubikeAgent 获取修铺自营信息]
     * @return [type] [description]
     */
    private function getXiubikeAgent()
    {
        if (!empty($this->xiubikeAgentId)) 
        {
            $keyArray=array("id","user_id","name","user_name","phone","address","province_id","province_name","city_id","city_name","district_id","district_name","status");//代理商基本信息
            $xiubikeAgentInfo = M_Mysqli_Class('cro', 'XiuAgentModel')->getAgentInfoByAttr(['id'=>$this->xiubikeAgentId]);
            $xiubikeAgentInfo=$this->setArray($keyArray,$xiubikeAgentInfo);
            return $xiubikeAgentInfo;
        }else{
            return $this->xiubikeAgent;
        }
    }

    /**
     * [applyBindXgFuntion 代理商发起绑定修哥申请]
     * @return [type] [description]
     */
    private function applyBindXgFuntion($parames)
    {
            unset($parames['action']);
            unset($parames['token']);
            $fixInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['xg_id']]);
            if ($fixInfo['status']==4){
                $outPut['status']="error";
                $outPut['code']="0119";
                $outPut['msg']="绑定失败,该修哥正在进行绑定";
                $outPut['data']="";
                return $outPut;
            }
            $fix_shop=array();//fix_shop 申请数据
            $fix_shop['type']=3;//代理商发起申请
            $fix_shop['fix_id']=$parames['xg_id'];
            $fix_shop['agent_id']=$parames['agent_id'];
            $data['id'] = $parames['xg_id'];
            $data['parent_id'] = $parames['agent_id'];
            $data['status'] = 4;
            M_Mysqli_Class('cro','FixShopModel')->trans_start();
            M_Mysqli_Class('cro','UserModel')->updateUser($data);//修改修哥状态为4
            M_Mysqli_Class('cro','FixShopModel')->addFixShop($fix_shop);
            M_Mysqli_Class('cro','FixShopModel')->trans_complete();
            if (M_Mysqli_Class('cro','FixShopModel')->trans_status() === FALSE) {
                $outPut['status']="error";
                $outPut['code']="0118";
                $outPut['msg']="网络问题，请重试";
                $outPut['data']="";
            }else{
                $outPut['status']="ok";
                $outPut['code']="2000";
                $outPut['msg']="申请成功，请等待修哥同意";
                $outPut['data']='';
            }
            return $outPut;
    }



    /**
     * [refuseXgFuntion 拒绝修哥绑定]
     * @param  [type] $condition [description]
     * @param  [type] $agent_id   [description]
     * @return [type]            [description]
     */
    // public function refuseXgFuntion($condition,$agent_id)
    // {
    //     $res =$this->updateXgFunction($condition);
    //     if ($res) {
    //         $data=[];
    //         $data['fix_id']=$condition['id'];
    //         $data['agent_id']=$agent_id;
    //         $data['type']=3;//拒绝
    //         $data['status']=1;
    //         $this->_addFixShopLog($data);//写入日志
    //         $outPut['status']="ok";
    //         $outPut['code']="2000";
    //         $outPut['msg']="绑定成功";
    //         $outPut['data']="";
    //     }else{
    //         $outPut['status']="error";
    //         $outPut['code']="0118";
    //         $outPut['msg']="绑定失败";
    //         $outPut['data']="";
    //     }
    //     return $outPut;
    // }

    /**
     * [bindXgFuntion 绑定修哥]
     * @return [type] [description]
     */
    public function bindXgFuntion($condition)
    {
        $msg_id=$condition['msg_id'];
        unset($condition['msg_id']);
        $res =$this->updateXgFunction($condition);
        if ($res) {
            //同意绑定申请
            M_Mysqli_Class('cro', 'FixShopModel')->updateFixShop(['id'=>$msg_id,'status'=>1]);
            $data=[];
            $data['fix_id']=$condition['id'];
            $data['agent_id']=$condition['parent_id'];
            $data['type']=1;//绑定
            $data['status']=1;
            $this->_addFixShopLog($data);//写入绑定日志
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="绑定成功";
            $outPut['data']="";
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="绑定失败";
            $outPut['data']="";
        }
        return $outPut;
    }

    /**
     * [unbindXgFuntion 解绑修哥]
     * @param  [type] $condition [description]
     * @return [type]            [description]
     */
    public function unbindXgFuntion($condition,$agent_id)
    {
        // $res =$this->updateXgFunction($condition);
        M_Mysqli_Class('cro','XiuOrdersModel')->trans_start();//事务开启
        M_Mysqli_Class('cro', 'UserModel')->updateUser($condition);
        $fixStatus = M_Mysqli_Class('cro','FixStatusModel')->getFixStatusByFixId($condition['id']);
        if ($fixStatus['num']>0){
            M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['status'=>0,'agent_id'=>0],['fix_id'=>$condition['id']]);//修哥收工,代理商id变为零
        }
        M_Mysqli_Class('cro','XiuOrdersModel')->trans_complete();
        if (M_Mysqli_Class('cro','XiuOrdersModel')->trans_status() != FALSE) {
            $this->_updateFixShopLog(['status'=>2],['fix_id'=>$condition['id'],'agent_id'=>$agent_id,'type'=>1]);//将该修哥的原绑定日志状态改为废除
            $data=[];
            $data['fix_id']=$condition['id'];
            $data['agent_id']=$agent_id;
            $data['type']=2;//解绑
            $data['status']=1;
            $this->_addFixShopLog($data);//写解绑定日志
            $outPut['status']="ok";
            $outPut['code']="2000";
            $outPut['msg']="解绑成功";
            $outPut['data']="";
        }else{
            $outPut['status']="error";
            $outPut['code']="0118";
            $outPut['msg']="解绑失败";
            $outPut['data']="";
        }
        return $outPut;
    }


    /**
     * [updateXgFunction 修改修哥数据方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function updateXgFunction($condition)
    {
        $res=M_Mysqli_Class('cro', 'UserModel')->updateUser($condition);
        return $res;
    }



    /**
     * [employeesFunction 获取我的员工]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function employeesFunction($parames)
    {
        $condition=[];
        $condition['parent_id']=$parames['agent_id'];
        $condition['role_flag']=3;
        $condition['status']=1;
        $res=M_Mysqli_Class('cro', 'UserModel')->getConditionXg($condition,$parames['page'],intval($parames['pageSize']));
        if (empty($res)) {
            return ;
        }
        $keyArray=array("id","username","name","tel","role_flag","status");
        foreach ($res as $k => $v) 
        {
            $res[$k]=$this->setArray($keyArray,$v);
        }
        $fixIdList=array_column($res, 'fix_id'); //获取修哥id数组集合
        $fixIdList=array_filter($fixIdList);//修哥id数组集合去空值
        if (!empty($fixIdList)) {
            $fixIconList=M_Mysqli_Class('cro', 'ImgModel')->getXgIconList($fixIdList,1,1);
        }else{
            $fixIconList='';
        }
        foreach($res as $k=>$v)
        {
            $res[$k]=$this->setArray($keyArray, $v);
            if (!empty($v['fix_id'])) 
            {
                    if (!empty($fixIconList)) 
                    {
                        foreach ($fixIconList as $s => $j) 
                        {
                            if ($j['attr_id']==$v['fix_id']) 
                            {
                                $res[$k]['xg_icon']=['hash'=>$j['img_hash'],'key'=>$j['img_key']];
                            }else{
                                $res[$k]['xg_icon']=['hash'=>'','key'=>''];
                            }
                        }
                    }else{
                        $res[$k]['xg_icon']=['hash'=>'','key'=>''];
                    }
            }else{
                    $res[$k]['xg_icon']=['hash'=>'','key'=>''];
            }
        }
        return $res;
    }


    /**
     * [searchEmployeesFunction 搜索修哥]
     * @param  [type] $search_data [description]
     * @return [type]          [description]
     */
    private function searchEmployeesFunction($parames)
    {
        $condition=[];
        $condition['status']=3;//状态为待代理商绑定
        $condition['role_flag']=3;//角色为修哥
        $condition['realname_status']=1;//已实名认证
        $res=M_Mysqli_Class('cro', 'UserModel')->getSearchUser($condition,$parames['search_data'],$parames['page'],intval($parames['pageSize']));
        if (empty($res)) {
            return ;
        }
        $xgIdList=array_column($res, 'id');
        $keyArray=array("id","username","name","icon","tel");
        //获取修哥头像
        $rea=M_Mysqli_Class('cro', 'ImgModel')->getXgIconList($xgIdList,1,1);

        foreach ($res as $k => $v) 
        {
            $res[$k]=$this->setArray($keyArray,$v);
            foreach ($rea as $s => $j) {
                if ($j['attr_id']==$v['id']) {
                   $res[$k]['icon']=['hash'=>$j['img_hash'],'key'=>$j['img_key']];
                }else{
                    $res[$k]['icon']=['hash'=>'','key'=>''];
                }
                
            }
        }
        return $res;
    }

    /**
     * [_addFixShopLog 写入绑定修哥日志]
     * @param [type] $fix_id  [description]
     * @param [type] $agent_id [description]
     */
    private function _addFixShopLog($data) 
    {
        $data['createtime']=time();
        $data['create_date']=date("Y-m-d,H:i:s",time());
        M_Mysqli_Class('cro', 'FixShopLogModel')->addFixShopLog($data);
        
    }

    /**
     * [_updateFixShopLog 更新绑定修哥日志]
     * @param [type] $fix_id  [description]
     * @param [type] $agent_id [description]
     */
    private function _updateFixShopLog($data,$wheres) 
    {
        M_Mysqli_Class('cro', 'FixShopLogModel')->updateFixShopLogByAttr($data,$wheres);
        
    }

    /**
     * [_checkFixShopLog 验证修哥是否绑定代理商]
     * @param  [type] $fix_id [description]
     * @return [type]         [description]
     */
    private function _checkFixShopLog($fix_id) 
    {
        $res=M_Mysqli_Class('cro', 'FixShopLogModel')->checkFixShopLog($fix_id,1,1);
        if($res>0){
             $outPut['status'] = 'error';
             $outPut['code']="1005";
            $outPut['msg'] = '该修哥已绑定代理商';
            $outPut['data'] = '';
            $this->setOutPut($outPut);
        }else{
            return true;
        }
    }
}

?>