<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class fixperfect extends MY_Controller {

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
            case "actionPostSearchShop"://搜索商家
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
                    $outPut['data']=$this->SearchShopFunction($parames);
                }
                break;
            case "actionPostFixShop"://获取修哥绑定的商家
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
                    $outPut['data']=$this->FixShopFunction($parames);
                }
                break;
            case "actionPostFixBindingShop"://修哥绑定商家
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('shop_id','店铺ID','numeric|min_length[1]|max_length[11]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['user_id'], $parames['token']);
                    $outPut=$this->FixBindingShopFunction($parames);
                }
                break;
            case "actionPostFixHome"://修哥首页
               $this->form_validation->set_data($parames);
               $this->form_validation->set_rules('fix_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                 if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['fix_id'], $parames['token']);
                    $parames['platform_id'] = '2';//修铺平台
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->FixHome($parames);
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
     * put方法
     */
    public function actionPut(){
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionFixStatusLocation"://修哥位置更新
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('fix_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
                $this->form_validation->set_rules('fix_location_longitude','经度','trim|min_length[1]|max_length[255]|required');
                $this->form_validation->set_rules('fix_location_latitude','纬度','trim|min_length[1]|max_length[255]|required');
                 if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $this->_checkUserLogin($parames['fix_id'], $parames['token']);
                    $outPut = $this->FixStatusLocationFunction($parames);
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
                $this->form_validation->set_rules('shop_id','店铺ID','trim|min_length[1]|max_length[11]');
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
            case 'actionGetFixLocation'://获取修哥位置
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('fix_id','修哥Id','numeric|min_length[1]|max_length[11]');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']='';
                    $outPut['data']=$this->GetFixLocationFunction($parames);
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
     * [SearchShop 搜索店铺方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function SearchShopFunction($parames)
    {
        $shopList=M_Mysqli_Class('cro', 'ShopModel')->getSearchShop('1',$parames['name']);
        if(is_array($shopList)){
            $keyArray=array(
                'id',
                'user_id',
                'name',
                'user_name',
                'dianpu_img',
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
            }
        }
        return $shopList;
    }

    /**
     * [FixHome 修哥首页信息方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function FixHome($parames){
        unset($parames['action']);
        unset($parames['token']);
        $fixInfo = M_Mysqli_Class('cro','OrderEvaluationModel')->evaluationAvg($parames);//修哥各评价平均值
        $keyArray=array('thy_avg','sve_avg','apd_avg');
        $avgArr=$this->setArray($keyArray, $fixInfo);

        $return['avg'] =round(array_sum($avgArr)/count($avgArr),1);//修哥评价平均分

        $return['num'] = M_Mysqli_Class('cro','XiuOrdersModel')->getFixOrder($parames['fix_id'],'3')['num'];//获取修哥已完成订单的数量
        $return['status']= M_Mysqli_Class('cro', 'UserModel')->getUserInfoByAttr(['id'=>$parames['fix_id']])['status'];
        $fixStatus = M_Mysqli_Class('cro','FixStatusModel')->getFixStatusByFixId($parames['fix_id']);
        $return['fix_status'] = $fixStatus['num']>0 ? $fixStatus['status'] : '0';//修哥开工状态
        $return['realname_status'] = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['fix_id']])['realname_status'];//实名认证状态
        $return['fixStstusTime'] = $this->fixStstusTime;
        return $return;
    }

    /**
     * [FixShopFunction 获取修哥所属店铺方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function FixShopFunction($parames){
        unset($parames['action']);
        unset($parames['token']);
        $parames['status'] = '1';
        $userInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr($parames);
        $keyArray=array('parent_id');
        $shop_id=$this->setArray($keyArray, $userInfo)['parent_id'];//修哥所属店铺ID
        $shopInfo = M_Mysqli_Class('cro','ShopModel')->getShopInfoByAttr(['id'=>$shop_id]);
        if (is_array($shopInfo)) {
            $keyArr=array(
                'id',
                'user_id',
                'name',
                'user_name',
                'dianpu_img',
                'province_id',
                'province_name',
                'city_id',
                'city_name',
                'district_id',
                'district_name'
            );
            $shopInfo=$this->setArray($keyArr, $shopInfo);
        }
        return $shopInfo;
    }

    /**
     * [FixBindingShopFunction 修哥绑定店铺方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function FixBindingShopFunction($parames){
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
            $outPut['msg']="您已提交过绑定申请，请耐心等待商家同意";
            $outPut['data']="";
        }else if ($userInfo['status']=='3') {
            $shopInfo = M_Mysqli_Class('cro','ShopModel')->getShopInfoByAttr(['id'=>$parames['shop_id']]);
            if (empty($shopInfo)) {
                $outPut['status']="error";
                $outPut['code']="1007";
                $outPut['msg']="选择的商家不存在";
                $outPut['data']="";
            }
            M_Mysqli_Class('cro','FixShopModel')->trans_start();
            $parames['fix_id'] = $parames['user_id'];
            $parames['type'] = '1';

            $data['id'] = $parames['user_id'];
            $data['parent_id'] = $parames['shop_id'];
            $data['status'] = '4';
            unset($parames['user_id']);

            M_Mysqli_Class('cro','FixShopModel')->addFixShop($parames);
            $result = M_Mysqli_Class('cro','UserModel')->updateUser($data);
            M_Mysqli_Class('cro','FixShopModel')->trans_complete();

            if (M_Mysqli_Class('cro','FixShopModel')->trans_status() === FALSE) {
                $outPut['status']="error";
                $outPut['code']="0118";
                $outPut['msg']="网络问题，请重试";
                $outPut['data']="";
            }else{
                $outPut['status']="ok";
                $outPut['msg']="申请成功，请等待商家同意";
                $outPut['code']="2000";
                $outPut['data']='';
            }
        }else{
            $outPut['status']="error";
            $outPut['code']="1008";
            $outPut['msg']="您的账号目前暂不可绑定商家";
            $outPut['data']="";
        }
        return $outPut;
    }

    /**
     * [FixStatusFunction 操作修哥状态方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
        private function FixStatusFunction($parames){
        $userInfo = M_Mysqli_Class('cro','UserModel')->getUserInfoByAttr(['id'=>$parames['fix_id']]);
        if ($userInfo['status']=='1') {
            switch ($parames['status']) {
                case '0'://修哥收工
                    $num = M_Mysqli_Class('cro','XiuOrdersModel')->getFixOrder($parames['fix_id'],1)['num'];
                    if ($num>0) {
                        $outPut['status']="error";
                        $outPut['code']="1009";
                        $outPut['msg']="还有订单未完成，暂时无法收工";
                        $outPut['data']="";
                    }else{
                        $result = M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['status'=>$parames['status']],['fix_id'=>$parames['fix_id']]);
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
                    $shopInfo = M_Mysqli_Class('cro','ShopModel')->getShopInfoByAttr(['id'=>$userInfo['parent_id']]);
                    if ($shopInfo['fix_status']=='1') {//判断修哥所在店铺营业状态：店铺收工状态无法开工
                        $result = 0;
                        $fixStatus = M_Mysqli_Class('cro','FixStatusModel')->getFixStatusByFixId($parames['fix_id']);
                        if ($fixStatus['num']>0) {//判断修哥是否有临时信息
                            $result = M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus(['status'=>$parames['status']],['fix_id'=>$parames['fix_id']]);
                        }else{
                            $data = array('fix_id' =>$parames['fix_id'] , 'status'=>'1','order_status'=>'0','shop_id'=>$shopInfo['id']);
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
                    }else{
                        $outPut['status']="error";
                        $outPut['code']="1010";
                        $outPut['msg']="店铺休息中，无法开工";
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
            $outPut['msg']="对不起，您暂时无法出工,请联系商家";
            $outPut['data']="";
        }
        return $outPut;
    }

    /**
     * [FixStatusLocation 修改修哥位置方法]
     * @param  [type] $parames [description]
     * @return [type]          [description]
     */
    private function FixStatusLocationFunction($parames){
        $data =[
                        'fix_location_longitude'=>$parames['fix_location_longitude'],
                        'fix_location_latitude'=>$parames['fix_location_latitude']
                    ];
        $wheres =[
                            'fix_id'=>$parames['fix_id'],
                            // 'status'=>'1',
                            // 'order_status'=>'0'
                        ];
        $result = M_Mysqli_Class('cro','FixStatusModel')->updateFixStatus($data,$wheres);
        $outPut['status']="ok";
        $outPut['code']="2000";
        $outPut['msg']="修改成功";
        $outPut['data']='';
        // if ($result>0) {
        //     $outPut['status']="ok";
        //     $outPut['code']="2000";
        //     $outPut['msg']="修改成功";
        //     $outPut['data']='';
        // }else{
        //     $outPut['status']="error";
        //     $outPut['code']="0118";
        //     $outPut['msg']="修改失败，请稍后重试";
        //     $outPut['data']=""; 
        // }
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
            $shopIds = array_column($fixShopResult, 'shop_id');
            $userResult = M_Mysqli_Class('cro','UserModel')->getUsersInfoByAttr(['id'=>$fixIds]);
            $shopResult = M_Mysqli_Class('cro','ShopModel')->getShopsInfoByAttr(['id'=>$shopIds]);
            foreach ($fixShopResult as $key => $value) {
                $return[$key]['id'] = $fixShopResult[$key]['id'];
                $return[$key]['fix_id'] = $fixShopResult[$key]['fix_id'];
                $return[$key]['shop_id'] = $fixShopResult[$key]['shop_id'];
                $return[$key]['create_date'] = $fixShopResult[$key]['create_date'];
                foreach ($userResult as $k => $v) {
                    if ($fixShopResult[$key]['fix_id']==$userResult[$k]['id']) {
                        $return[$key]['user_name'] = $userResult[$k]['name'];
                        $return[$key]['fix_phone'] = $userResult[$k]['username'];
                        $iconData=M_Mysqli_Class('cro', 'ImgModel')->getImgInfoByAttr(['attr_id'=>$userResult[$k]['id'],'attr'=>1,'type'=>1]);//头像 
                        $return[$key]['user_img'] =!empty($iconData) ? ["hash"=>$iconData['img_hash'],"key"=>$iconData['img_key']] : ["hash"=>'',"key"=>CRO_IMG.$userResult[$k]['icon']];
                    }
                }
                foreach ($shopResult as $i => $j) {
                    if ($fixShopResult[$key]['shop_id']==$shopResult[$i]['id']) {
                        $return[$key]['shop_name'] = $shopResult[$i]['name'];
                        $return[$key]['shop_address'] = $shopResult[$i]['city'];
                        $iconData=M_Mysqli_Class('cro', 'ImgModel')->getImgInfoByAttr(['attr_id'=>$shopResult[$i]['id'],'attr'=>2,'type'=>4]);//店铺照片
                        $return[$key]['shop_img'] =!empty($iconData) ? ["hash"=>$iconData['img_hash'],"key"=>$iconData['img_key']] : ["hash"=>'',"key"=>CRO_IMG.$shopResult[$i]['dianpu_img']];
                    }
                }
            }
        }
        return $return;
    }

    private function GetFixLocationFunction($parames){
        $result = M_Mysqli_Class('cro','FixStatusModel')->getFixStatusByFixId($parames['fix_id']);
        $keyArray=array( 'id', 'fix_id', 'fix_location_longitude', 'fix_location_latitude');
        $result=$this->setArray($keyArray, $result);
        return $result;
    }
}
?>
