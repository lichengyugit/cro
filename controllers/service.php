<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class service extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 服务入口
     */
    public function index(){
        F()->Resource_module->setTitle('服务');
        F()->Resource_module->setJsAndCss(array(
        'service_page'
                ), array(
                'main'
        ));
        //$this->smarty->view('service/service.phtml');
        $this->serviceList();
    }

    /**
     * 服务列表页
     */
    public function serviceList() {
        F()->Resource_module->setTitle('服务');
        F()->Resource_module->setJsAndCss(array(
            'service_page'
        ), array(
            'main'
        ));
        $this->form_validation->setRequestMethod('get');
        $this->form_validation->set_rules('id', '服务id', 'trim');
        $this->form_validation->run();
        $data = $this->form_validation->get_validationed_data();
        $serviceList = M_Mysqli_Class('mssp_www', 'ServiceModel')->getAllService();
        if(empty($data)){
            $data['id']=$serviceList[0]['id'];
        }
        $serviceChildList = M_Mysqli_Class('mssp_www', 'ServiceChildModel')->getChildServiceDataByServiceId($data['id']);
        $serviceInfo = M_Mysqli_Class('mssp_www', 'ServiceModel')->getOneService($data['id']);
        $getServicePic = M_Mysqli_Class('mssp_www', 'PictureModel')->getPcitureByType(4,$data['id']);
        if(count($getServicePic)>0){
        $serviceInfo['pic_logo_url']=PIC_URL.'/'.$getServicePic[0]['deeppath'].'/'.$getServicePic[0]['filename'];
        }
        $getServicePic = M_Mysqli_Class('mssp_www', 'PictureModel')->getPcitureByType(11,$data['id']);
        if(count($getServicePic)>0){
            $serviceInfo['pic_url']=PIC_URL.'/'.$getServicePic[0]['deeppath'].'/'.$getServicePic[0]['filename'];
        }
        $qualysGoodsList = M_Mysqli_Class('mssp_www', 'ProductQualysModel')->getQualys();
        $tenableGoodsList = M_Mysqli_Class('mssp_www', 'ProductTenablesModel')->getTenables();
        $coreImpactsGoodsList = M_Mysqli_Class('mssp_www', 'ProductCoreImpactsModel')->getCoreImpactsTwo();
        $allGoods=array_merge($qualysGoodsList,$tenableGoodsList,$coreImpactsGoodsList);
        $countNum=count($allGoods)-1;
        $randNum=rand(0,$countNum);
        $randGoods=array($allGoods[rand(0,$countNum)],$allGoods[rand(0,$countNum)],$allGoods[rand(0,$countNum)]);
        //print_r($randGoods);
        if(is_array($randGoods)){
            foreach ($randGoods as $k=>$v){
                if(strstr(strtolower($v['name']),'core')){
                    $type_id=8;
                    $randGoods[$k]['url']='/product/impactList';
                }
                elseif(strstr(strtolower($v['name']),'qualys')){
                    $type_id=9;
                    $randGoods[$k]['url']='/product/qualysList?id='.$v['id'];
                }
                else{
                    $type_id=10;
                    $randGoods[$k]['url']='/product/tenableList?id='.$v['id'];
                }
                $randGoodsPic=M_Mysqli_Class('mssp_www', 'PictureModel')->getPcitureByType($type_id,$v['id']);
                if(count($randGoodsPic)>0){
                   $randGoods[$k]['logo'] = PIC_URL.'/'.$randGoodsPic[0]['deeppath'].'/'.$randGoodsPic[0]['filename'];
                }
             }
        }
        $video = M_Mysqli_Class('mssp_www', 'VideoModel')->getVideoByType(7,$data['id']);
        if($video){
            $videoPage=floor(count($video)/3);
            if($videoPage>1){
                $videoPageHtml='';
                for($i=1;$i<=$videoPage;$i++){
                    $videoPageHtml.='<li ';
                    if($videoPage==1){
                        $videoPageHtml.=' class="info-cur" ';
                    }
                    $videoPageHtml.=' id="mypic'.$videoPage.'" sid="'.$videoPage.'">';
                    $videoPageHtml.='<span>'.$videoPage.'</span>';
                    $videoPageHtml.='</li>';
                }
                $this->smarty->assign('videoPageHtml',$videoPageHtml);
            }
            $this->smarty->assign('video',$video);
        }
        $this->smarty->assign('id',$data['id']);
        $this->smarty->assign('serviceInfo',$serviceInfo);
        $this->smarty->assign('serviceChildList',$serviceChildList);
        $this->smarty->assign('serviceList',$serviceList);
        $this->smarty->assign('randGoods',$randGoods);
        $this->smarty->assign('video',$video);
        $this->smarty->view('services/service.phtml');
    }

}