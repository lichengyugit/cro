<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class consult extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 咨询入口
     */
    public function index(){
//         F()->Resource_module->setTitle('咨询列表');
//         F()->Resource_module->setJsAndCss(array(
//                 'consult_page'
//                 ), array(
//                 'consult',
//                 'layout'
//         ));
//         $this->smarty->view('consults/consultList.phtml');
        $this->consultList();
    }

    /**
     * 咨询主页
     */
    public function consultList() {
        F()->Resource_module->setTitle('资讯列表');
        F()->Resource_module->setJsAndCss(array(
            'consult_page'
        ), array(
            'consult',
            'layout'
        ));
        $industryConsultList = M_Mysqli_Class('mssp_www', 'ArticleModel')->getAllConsultByType(1,1,6);
        $companyConsultList = M_Mysqli_Class('mssp_www', 'ArticleModel')->getAllConsultByType(1,2,6);
        if(is_array($companyConsultList)){
            foreach($companyConsultList as $k=>$v){
                if($companyConsultList[$k]['child_type']=='1'){
                    $picInfo = M_Mysqli_Class('mssp_www', 'PictureModel')->getPcitureByType(3,$v['id']);
                }else{
                    $picInfo = M_Mysqli_Class('mssp_www', 'PictureModel')->getPcitureByType(2,$v['id']);
                }
                if($picInfo){
                    $companyConsultList[$k]['pic_url']=PIC_URL.'/'.$picInfo['0']['deeppath'].'/'.$picInfo['0']['filename'];
                }
             }
        }
        if(is_array($industryConsultList)){
            foreach($industryConsultList as $k=>$v){
                if($industryConsultList[$k]['child_type']=='1'){
                    $picInfo = M_Mysqli_Class('mssp_www', 'PictureModel')->getPcitureByType(3,$v['id']);
                }else{
                    $picInfo = M_Mysqli_Class('mssp_www', 'PictureModel')->getPcitureByType(2,$v['id']);
                }
                if($picInfo){
                    $industryConsultList[$k]['pic_url']=PIC_URL.'/'.$picInfo['0']['deeppath'].'/'.$picInfo['0']['filename'];
                }
            }
        }
        $this->smarty->assign('industryConsultList',$industryConsultList);
        $this->smarty->assign('companyConsultList',$companyConsultList);
        $this->smarty->view('consults/consultList.phtml');
    }
    
    /**
     * 咨询详情页
     */
    public function consultDetail(){
           F()->Resource_module->setTitle('资讯详情');
           F()->Resource_module->setJsAndCss(array(),array(
                'main'
           ));
           $this->form_validation->setRequestMethod('get');
           $this->form_validation->set_rules('article_title', '文章標題', 'alpha_dash');
           $this->form_validation->set_rules('id', '文章id', 'alpha_dash');
           $this->form_validation->run();
           $data = $this->form_validation->get_validationed_data();
           $consultInfo = M_Mysqli_Class('mssp_www', 'ArticleModel')->getOneArticle($data['id']);
           if($consultInfo){
               $interestedInfo = M_Mysqli_Class('mssp_www', 'ArticleModel')->getAllConsultByType(1,$consultInfo['child_type'],3);
               $this->smarty->assign('interestedInfo',$interestedInfo);
               $this->smarty->assign('consultInfo',$consultInfo);
               $this->smarty->view('consult/consultDetail.phtml');
           }else{
               $this->redirect("consults/consulDetail.phtml");
           }
    }
    
    /**
     * AJAX咨询加载页
     */
    public function ajaxLoadConsultList(){
           $this->form_validation->set_rules('child_type', '子分類', 'alpha_dash');
           $this->form_validation->set_rules('current_page', '層數', 'alpha_dash');
           
           $this->form_validation->run();
           $data = $this->form_validation->get_validationed_data();
           $pages=6;
           if($data['current_page']<1){
               $data['current_page']=1;
           }
           
           $limit =($data['current_page']-1) * $pages .",". $pages; 
           $dataList = M_Mysqli_Class('mssp_www', 'ArticleModel')->getAllConsultByType(1,$data['child_type'],$limit);
           if(is_array($dataList)){
               foreach($dataList as $k=>$v){
                   if($dataList[$k]['child_type']=='1'){
                       $picInfo = M_Mysqli_Class('mssp_www', 'PictureModel')->getPcitureByType(3,$v['id']);
                   }else{
                       $picInfo = M_Mysqli_Class('mssp_www', 'PictureModel')->getPcitureByType(2,$v['id']);
                   }
                   if($picInfo){
                       $dataList[$k]['pic_url']=PIC_URL.'/'.$picInfo['0']['deeppath'].'/'.$picInfo['0']['filename'];
                   }else{
                       $dataList[$k]['pic_url']="";
                   }
               }
           }
           if($dataList){
               $dataList=$this->_ajaxSucc($dataList);
           }else{
               $dataList=$this->_ajaxFail("empty");
           }
           
           
           
    }

}