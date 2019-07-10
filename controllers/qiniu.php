<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 * 七牛
 */
class qiniu extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();
    }

    /**
     * get方法
     */
    public function actionGet(){
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionGetQiniuToken"://获取七牛token
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('user_id','用户Id','numeric|min_length[1]|max_length[11]|required');
                $this->form_validation->set_rules('token','用户token','trim|min_length[1]|max_length[50]|required');
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
                    $outPut['data']=$this->getQiniuTokenFunction();//获取七牛token
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
     * [getQiniuTokenFunction 获取七牛token方法]
     * @return [type] [description]
     */
    private function getQiniuTokenFunction()
    {
        return F()->Qiniu_module->createToken();
    }

}
?>
