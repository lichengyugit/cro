<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 *  商家修哥控制器
 */
class shopxg extends MY_Controller {

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
                        $outPut['data']=$this->employeesFunction($parames);
                    }
                    break;
            case "actionGetBindXgMessage"://获取我的员工
                    $this->form_validation->set_data($parames);
                    $this->form_validation->set_rules('user_id','商家用户Id','numeric|min_length[1]|max_length[11]|required');
                    $this->form_validation->set_rules('shop_id','商家Id','numeric|min_length[1]|max_length[11]|required');
                    $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
                    if ($this->form_validation->run() === FALSE) {
                        $outPut['status']="error";
                        $outPut['msg']=$this->form_validation->validation_error();
                        $outPut['data']="";
                    }else{
                        $this->_checkUserLogin($parames['user_id'], $parames['token']);
                        $outPut['status']="ok";
                        $outPut['msg']="";
                        $outPut['data']=$this->getBindXgMessage($parames);
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
                        $this->form_validation->set_rules('user_id','商家用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('search_data','搜索信息','trim|min_length[1]|max_length[50]|required');
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
                            $outPut['data']=$this->searchEmployeesFunction($parames['search_data']);
                        }
                        break;
                case "actionApplyBindXg"://商家发起绑定修哥方法
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','商家Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('shop_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('xg_id','修哥Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['code']="1001";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $this->_checkFixShopLog($parames['xg_id']);//验证修哥是否被绑定过
                            $this->_checkShopXg($parames['shop_id']);//验证该商家下的修哥数量
                            $outPut=$this->applyBindXgFuntion($parames);//绑定修哥
                        }
                        break;
                case "actionBindXg"://绑定修哥
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('shop_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('xg_id','修哥Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('fix_shop_id','申请消息Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->run();
                        if ($this->form_validation->run() === FALSE) {
                            $outPut['status']="error";
                            $outPut['msg']=$this->form_validation->validation_error();
                            $outPut['data']="";
                        }else{
                            $this->_checkUserLogin($parames['user_id'], $parames['token']);
                            $this->_checkFixShopLog($parames['xg_id']);//验证修哥是否被绑定过
                            $this->_checkShopXg($parames['shop_id']);//验证该商家下的修哥数量
                            $condition=[];
                            $condition['id']=$parames['xg_id'];//修哥id
                            $condition['parent_id']=$parames['shop_id'];//商铺id
                            $condition['fix_shop_id']=$parames['fix_shop_id'];//申请消息Id
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
     * [actionPut 商家中心put方法]
     * @return [type] [description]
     */
    public function actionPut()
    {
                $parames=$this->parames;
                switch ($parames['action'])
                {
                    case "actionUnbindXg"://商家拒绝修哥绑定
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','商家Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('shop_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
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
                            $condition['status']=3;//状态改为3 待商家绑定
                            $outPut=$this->refuseXgFuntion($condition,$parames['shop_id']);//拒绝修哥
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
     * [actionDelete 商家中心put方法]
     * @return [type] [description]
     */
    public function actionDelete()
    {
                $parames=$this->parames;
                switch ($parames['action'])
                {
                    case "actionDeleteXg"://解绑修哥
                        $this->form_validation->set_data($parames);
                        $this->form_validation->set_rules('user_id','商家Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('token','商家token','trim|min_length[1]|max_length[50]|required');
                        $this->form_validation->set_rules('xg_id','修哥Id','numeric|min_length[1]|max_length[11]|required');
                        $this->form_validation->set_rules('shop_id','商铺Id','numeric|min_length[1]|max_length[11]|required');
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
                            $condition['status']=3;//状态改为3 待商家绑定
                            $outPut=$this->unbindXgFuntion($condition,$parames['shop_id']);//解绑修哥
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
     * [applyBindXgFuntion 商家发起绑定修哥申请]
     * @return [type] [description]
     */
    private function applyBindXgFuntion($parames)
    {
            unset($parames['action']);
            unset($parames['token']);
            $fixInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['xg_id']]);
            if ($fixInfo['status']==4){
                $outPut['status']="error";
                $outPut['msg']="绑定失败,该修哥正在进行绑定";
                $outPut['data']="";
                return $outPut;
            }
            $fix_shop='';//fix_shop 申请数据
            $fix_shop['type']=2;//商家发起申请
            $fix_shop['fix_id']=$parames['xg_id'];
            $fix_shop['shop_id']=$parames['shop_id'];
            $data['id'] = $parames['xg_id'];
            $data['parent_id'] = $parames['shop_id'];
            $data['status'] = 4;
            M_Mysqli_Class('cro','FixShopModel')->trans_start();
            M_Mysqli_Class('cro','UserModel')->updateUser($data);//修改修哥状态为4
            M_Mysqli_Class('cro','FixShopModel')->addFixShop($fix_shop);
            M_Mysqli_Class('cro','FixShopModel')->trans_complete();
            if (M_Mysqli_Class('cro','FixShopModel')->trans_status() === FALSE) {
                $outPut['status']="error";
                $outPut['msg']="网络问题，请重试";
                $outPut['data']="";
            }else{
                $outPut['status']="ok";
                $outPut['msg']="申请成功，请等待修哥同意";
                $outPut['data']='';
            }
            return $outPut;
    }

    /**
     * [_checkShopXg 商家修哥数量]
     * @param  [type] $shop_id [description]
     * @return [type]          [description]
     */
    public function _checkShopXg($shop_id)
    {
        $res=M_Mysqli_Class('cro', 'UserModel')->countXgNumbers($shop_id);
        if ($res>=3) {
            $outPut['status']="error";
            $outPut['code']="1100";
            $outPut['msg']="修哥数量超过三个,添加更多需支付300元";
            $outPut['data']="";
            $this->setOutPut($outPut);
        }
        return true;
        
    }

    /**
     * [refuseXgFuntion 拒绝修哥绑定]
     * @param  [type] $condition [description]
     * @param  [type] $shop_id   [description]
     * @return [type]            [description]
     */
    public function refuseXgFuntion($condition,$shop_id)
    {
        $res =$this->updateXgFunction($condition);
        if ($res) {
            $data=[];
            $data['fix_id']=$condition['id'];
            $data['shop_id']=$shop_id;
            $data['type']=3;//拒绝
            $data['status']=1;
            $this->_addFixShopLog($data);//写入日志
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
     * [bindXgFuntion 绑定修哥]
     * @return [type] [description]
     */
    public function bindXgFuntion($condition)
    {
        $fix_shop_id=$condition['fix_shop_id'];
        unset($condition['fix_shop_id']);
        $res =$this->updateXgFunction($condition);
        if ($res) {
            //同意绑定申请
            M_Mysqli_Class('cro', 'FixShopModel')->updateFixShop(['id'=>$fix_shop_id,'status'=>1]);
            $data=[];
            $data['fix_id']=$condition['id'];
            $data['shop_id']=$condition['parent_id'];
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
    public function unbindXgFuntion($condition,$shop_id)
    {
        $res =$this->updateXgFunction($condition);
        if ($res) {
            $this->_updateFixShopLog(['status'=>2],['fix_id'=>$condition['id'],'shop_id'=>$shop_id,'type'=>1]);//将该修哥的原绑定日志状态改为废除
            $data=[];
            $data['fix_id']=$condition['id'];
            $data['shop_id']=$shop_id;
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
        $condition['parent_id']=$parames['shop_id'];
        $condition['role_flag']=3;
        $condition['status']=1;
        $res=M_Mysqli_Class('cro', 'UserModel')->getUsersInfoByAttr($condition);
        $keyArray=array("id","username","name","role_flag","status");
        foreach ($res as $k => $v) 
        {
            $res[$k]=$this->setArray($keyArray,$v);
        }
        return $res;
    }


    /**
     * [searchEmployeesFunction 搜索修哥]
     * @param  [type] $search_data [description]
     * @return [type]          [description]
     */
    private function searchEmployeesFunction($search_data)
    {
        $condition=[];
        $condition['status']=3;//状态为待商家绑定
        $condition['role_flag']=3;//角色为修哥
        $condition['realname_status']=1;//已实名认证
        $res=M_Mysqli_Class('cro', 'UserModel')->getSearchUser($condition,$search_data);
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
                $res[$k]['icon']=['hash'=>$j['img_hash'],'key'=>$j['img_key']];
            }
        }
        return $res;
    }

    /**
     * [_addFixShopLog 写入绑定修哥日志]
     * @param [type] $fix_id  [description]
     * @param [type] $shop_id [description]
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
     * @param [type] $shop_id [description]
     */
    private function _updateFixShopLog($data,$wheres) 
    {
        M_Mysqli_Class('cro', 'FixShopLogModel')->updateFixShopLogByAttr($data,$wheres);
        
    }

    /**
     * [_checkFixShopLog 验证修哥是否绑定商家]
     * @param  [type] $fix_id [description]
     * @return [type]         [description]
     */
    private function _checkFixShopLog($fix_id) 
    {
        $res=M_Mysqli_Class('cro', 'FixShopLogModel')->checkFixShopLog($fix_id,1,1);
        if($res>0){
             $outPut['status'] = 'error';
             $outPut['code']="1005";
            $outPut['msg'] = '该修哥已绑定商铺';
            $outPut['data'] = '';
            $this->setOutPut($outPut);
        }else{
            return true;
        }
    }
}

?>