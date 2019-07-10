<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class membercenter extends MY_Controller {

    public function __construct() { 
        parent::__construct();
        $this->_checkUserLogin();
        $this->pagesize=6;
        F()->Resource_module->setJsAndCss(array(
            'personcenter_page'
        ), array(
            'layout',
            'person'
        ));
    }
    
    public function index(){
        $this->centerFiles();
    }
    
    /**
     *
     */
    public function centerFiles(){
        $user = $this->session->userdata('userInfo');
        
        $data['mobile_phone'] = $user['mobile_phone'];
        $data['email'] = $user['email'];
        $data['company_name'] = $user['company_name'];
        $data['address'] = $user['address'];
        F()->Resource_module->setTitle('个人中心-总览');
        F()->Resource_module->setJsAndCss(array(
                'personcenter_page'
        ), array(
                'layout',
                'person'
        ));
        //print_r($data);exit;
        $user = $this->session->userdata('userInfo');
        $data= M_Mysqli_Class('mssp_user', 'UsersModel')->getUserDataById($user['id']);
        $this->smarty->assign('active',"Files");
        $this->smarty->assign('users',$data);
        $this->smarty->view('person_center/person_files.phtml');
    
    }
    
    public function ajaxReviseUserInfo(){
        if (!$this->_checkPost()) {
            return;
        }
        $this->form_validation->setRequestMethod('post');
        $this->form_validation->set_rules('company_name','公司名称','trim|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('address','地址','trim|min_length[1]|max_length[100]');
        $this->form_validation->run();
        if ($this->form_validation->run() === FALSE) {
            return $this->_ajaxFail($this->form_validation->validation_error());
        }
        $data = $this->form_validation->get_validationed_data();
        $user = $this->session->userdata('userInfo');
        $updResult = M_Mysqli_Class('mssp_user', 'UsersModel')->updataUserInfoByUid($user['id'],$data);
        if($updResult){
            return $this->_ajaxSucc('updOk');
        }else{
            return $this->_ajaxFail('updError');
        }
       
    }
    
    /**
     * 用户批量删除用户订单
     */
    public function ajaxDeleteMemberOrders(){
        $this->form_validation->set_rules('subBoxOrderArry','数组-订单id','required');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        $sessionInfo= $this->session->userdata("userInfo");
        $data['id'] = $sessionInfo['id'];
        if(is_array($data)){
            foreach($data['subBoxOrderArry'] as $k=>$v){
                M_Mysqli_Class('mssp_www', 'OrderModel')->delectOrderByOid($v,$data['user_id']);
            }
            return $this->_ajaxSucc('delectOk');
        }else{
            return $this->_ajaxFail('Must_be_Choose_One');
        }
    }
    
    /**
     *  用户单独删除 
     */
    public function deleteOrder(){
        $this->form_validation->setRequestMethod('get');
        $this->form_validation->set_rules('id','数组-订单id','required');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        $sessionInfo= $this->session->userdata("userInfo");
        $data['id'] = $sessionInfo['id'];
        $delectInfo = M_Mysqli_Class('mssp_www', 'OrderModel')->delectOrderByOid($data['id'],$data['user_id']);
        if($delectInfo){
            return $this->_ajaxSucc('delectOk');
        }else{
            return $this->_ajaxFail('delete_Error');
        }
        
    }
    
    
    /**
     * 重置密码
     */
    public function ResetUserPassword(){
        $this->form_validation->set_rules('oldpwd', '旧密码', 'required|min_length[6]|max_length[16]');
        $this->form_validation->set_rules('newpwd', '新密码', 'required|min_length[6]|max_length[16]');
        $this->form_validation->set_rules('confirmpwd', '确认新密码', 'required|min_length[6]|max_length[16]');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        if($data['newpwd'] != $data['confirmpwd']){
            return $this->_ajaxFail('Different_New_Password');
        }
        $sessionInfo= $this->session->userdata("userInfo");
        $data['id'] = $sessionInfo['id'];
        $data['mobile_phone'] = $this->session->userdata("mobile_phone");
        $check = M_Mysqli_Class('mssp_user', 'UsersModel')->checkUserPassword($data);
        if($check['COUNT(1)'] == 1){
            $resetInfo = M_Mysqli_Class('mssp_user', 'UsersModel')->resetPassword($data);
            return $this->_ajaxSucc('Ok');
        }else{
            return $this->_ajaxFail('Password_Error');
        }
    }
    
    
    
    /**
     * 消息中心
     */
    public function centerMessages(){
        F()->Resource_module->setTitle('个人中心-消息中心');
        $this->form_validation->setRequestMethod('GET');
        $this->form_validation->set_rules('page', '页码', 'trim|default_value[1]');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        $url = 'memberCenter/centerMessages';
        $sessionInfo= $this->session->userdata("userInfo");
        $data['id'] = $sessionInfo['id'];
        $message = M_Mysqli_Class('mssp_www', 'MessageModel')->getMessageByUid(intval($data['page']), $this->pagesize,$data['id']);
        $count = M_Mysqli_Class('mssp_www', 'MessageModel')->countMessageById($data['id']);
        $paginationHtml = F()->Pagination_module->wwwPaginationHtml($data['page'], $count, $this->pagesize, $url);
        $this->smarty->assign('message', $message);
        $this->smarty->assign('page', $paginationHtml);
        $this->smarty->assign('active',"Message");
        $this->smarty->view('person_center/person_messages.phtml');
    }
    
    /**
     * ajax标记已阅读
     */
    public function ajaxReadMessage(){
        F()->Resource_module->setTitle('个人中心-消息中心');
        $this->form_validation->set_rules('id', '消息id', 'required');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        $sessionInfo= $this->session->userdata("userInfo");
        $data['id'] = $sessionInfo['id'];
        $data['status'] = "1";
        $updataInfo = M_Mysqli_Class('mssp_www', 'MessageModel')->updMessageById($data);
        if($updataInfo){
            return $this->_ajaxSucc('Ok');
        }
    }
    
    /**
     * ajax标记已阅读
     */
    public function ajaxDeleteMemberMessage(){
        $this->form_validation->set_rules('subBoxMessageArry','数组-订单id','required');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        $sessionInfo= $this->session->userdata("userInfo");
        $data['id'] = $sessionInfo['id'];
        if(is_array($data)){
            foreach($data['subBoxMessageArry'] as $k=>$v){
                M_Mysqli_Class('mssp_www', 'MessageModel')->delectMessageByMid($v,$data['user_id']);
            }
            return $this->_ajaxSucc('Ok');
        }else{
            return $this->_ajaxFail('Must_be_Choose_One');
        }
    }
    
    
    /**
     *  用户单独删除消息
     */
    public function deleteMessage(){
        $this->form_validation->setRequestMethod('get');
        $this->form_validation->set_rules('id','数组-订单id','required');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        $sessionInfo= $this->session->userdata("userInfo");
        $data['id'] = $sessionInfo['id'];
        $data['status'] = 2; 
        $delectInfo = M_Mysqli_Class('mssp_www', 'MessageModel')->updMessageById($data);
        if($delectInfo){
            return $this->_ajaxSucc('Ok');
        }else{
            return $this->_ajaxFail('delete_Error');
          }
    
    }
    /**
     * 消息中心
     */
    public function centerOrders(){
        F()->Resource_module->setTitle('个人中心-订单');
        $this->form_validation->setRequestMethod('GET');
        $this->form_validation->set_rules('page', '页码', 'trim|default_value[1]');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        $url = 'memberCenter/centerOrders';
        $sessionInfo= $this->session->userdata("userInfo");
        $data['id'] = $sessionInfo['id'];
        $orderList = M_Mysqli_Class('mssp_www', 'OrderModel')->gerUserOrdersByUid(intval($data['page']), $this->pagesize,$data['id']);
        $count = M_Mysqli_Class('mssp_www', 'OrderModel')->countOrderById($data['id']);
        $paginationHtml = F()->Pagination_module->wwwPaginationHtml($data['page'], $count, $this->pagesize, $url);
        $this->smarty->assign('orderList',$orderList);
        $this->smarty->assign('page', $paginationHtml);
        $this->smarty->assign('active',"Orders");
        $this->smarty->view('person_center/person_orders.phtml');
    }
    
    /**
     * 合同中心
     */
    public function  centerContract(){
        if (!$this->_checkGet()) {
            return;
        }
        F()->Resource_module->setTitle('个人中心-合同');
        $this->smarty->assign('active',"Contract");
        $this->smarty->view('person_center/person_contracts.phtml');
        
    }   
    
    /**
     * 个人总览
     */
    public function centerFeedbacks(){
        F()->Resource_module->setTitle('个人中心-合同');
        $this->smarty->assign('active',"Feedbacks");
        $this->smarty->view('person_center/person_feedbacks.phtml');
    }
    

      
} 
