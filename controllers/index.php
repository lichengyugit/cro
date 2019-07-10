<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class Index extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->smarty->assign('resourceUrl', RESOURCE_URL);
        $this->smarty->assign('baseUrl', BASE_URL);
    }
    
    /**
     * 首页入口
     */
    public function index(){
        $this->smarty->view('question/404.phtml');
    }


   /**
    * [addTest 增加数据测试]
    */
    public function addTest()
    {
        $newData=[];
        // $newData['user_id']=2537;
        // $newData['receiver']='贼娃子dd';
        // $newData['receiver_phone']=17602153732;
        // $newData['receiver_area']='上海2';
        // $newData['receiver_address']='杨浦3';
        // $newId=M_Mysqli_Class('cro_test','AddressModel')->addArticle($newData);
        $newId=M_Mysqli_Class('cro_test','AddressModel')->getAllConsultByType();
        var_dump($newId);
    }

    /**
     * [updateTest 修改数据测试]
     * @return [type] [description]
     */
//     public function updateTest()
//     {
//         $updateData['id']=25;
//         $updateData['receiver_area']='贼娃山庄';
//         M_Mysqli_Class('cro_test','AddressModel')->updateArticle($updateData);
//     }

//     public function testCon()
//     {
//         // $this->db->select('id');
//         // $this->db->from('sx_address');
//         // $this->db->get()
//         $res=M_Mysqli_Class('cro_test','AddressModel')->testFun(1);;
//         echo "<pre>";
//         print_r($res);
//         echo "</pre>";
//     }
}