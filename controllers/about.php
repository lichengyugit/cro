<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
class about extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 关于入口
     */
    public function index(){
        $this->aboutus();
    }
    
    /**
     * 关于主页
     */
    public function aboutus(){
        F()->Resource_module->setTitle('关于');
        F()->Resource_module->setJsAndCss(array(
        'about_page'
                ), array(
                'about',
                'layout'
        ));
        $this->smarty->view('about/aboutus.phtml');
    }
    
    /**
     * 公司介绍
     */
    public function company_introduction(){
        F()->Resource_module->setTitle('公司介绍');
        F()->Resource_module->setJsAndCss(array(
        'about_page'
                ), array(
                'about',
                'layout'
        ));
        $this->smarty->view('about/company_introduction.phtml');
    }
    
    /**
     * 荣誉资质
     */
    public function honor_qualification(){
        F()->Resource_module->setTitle('荣誉资质');
        F()->Resource_module->setJsAndCss(array(
        'about_page'
                ), array(
                'about',
                'layout'
        ));
        $this->smarty->view('about/honor_qualification.phtml');
    }
    
    /**
     * 公司团队
     */
    public function company_team(){
        F()->Resource_module->setTitle('公司团队');
        F()->Resource_module->setJsAndCss(array(
        'about_page'
                ), array(
                'about',
                'layout'
        ));
        $this->smarty->view('about/company_team.phtml');
    }
    
    /**
     * 联系我们
     */
    public function join_us(){
        F()->Resource_module->setTitle('联系我们');
        F()->Resource_module->setJsAndCss(array(
        'about_page'
                ), array(
                'about',
                'layout'
        ));
        $this->smarty->view('about/join_us.phtml');
    }
    
    /**
     * 加入我们
     */
    
    public function contact_us(){
        F()->Resource_module->setTitle('加入我们');
        F()->Resource_module->setJsAndCss(array(
        'about_page'
                ), array(
                'about',
                'layout'
        ));
        $this->smarty->view('about/contact_us.phtml');
    }
}