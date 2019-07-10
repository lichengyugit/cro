<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}
/**
 *
 * 活动和弹出图控制器
 */
class activity extends MY_Controller {


    public function __construct() {
        parent::__construct();
        $this->parames=$this->getParames();//调用http流方法
    }

    /**
     * get方法 获取用户信息
     * 
     */
    public function actionGet(){
        $parames=$this->parames;
        switch ($parames['action'])
        {
            case "actionGetActivity"://获取活动
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('role_flag','角色Id','trim|min_length[1]|max_length[6]|required');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->activityFunction($parames);
                }
                break;
            case "actionGetAdv"://获取广告
                $this->form_validation->set_data($parames);
                $this->form_validation->set_rules('role_flag','角色Id','trim|min_length[1]|max_length[6]|required');
                $this->form_validation->set_rules('adv_type','广告显示类别','trim|min_length[1]|max_length[6]');
                if ($this->form_validation->run() === FALSE) {
                    $outPut['status']="error";
                    $outPut['code']="1001";
                    $outPut['msg']=$this->form_validation->validation_error();
                    $outPut['data']="";
                }else{
                    $outPut['status']="ok";
                    $outPut['code']="2000";
                    $outPut['msg']="";
                    $outPut['data']=$this->advFunction($parames);
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
     * [activityFunction 获活动]
     * @param [type] $parames [description]
     */
    public function activityFunction($parames)
    {
        $condition=[];
        if ($parames['role_flag']==0) 
        {
            $condition['activity_port']=[0,1];
        }elseif ($parames['role_flag']==3) {
            $condition['activity_port']=[0,2];
        }elseif ($parames['role_flag']==4) {
            $condition['activity_port']=[0,3];
        }
        $condition['activity_status']=1;
        //查询图片数据
        $activityInfo = M_Mysqli_Class('cro', 'ActivityModel')->getActivitysInfoByAttr($condition);
        if (empty($activityInfo)) 
        {
            return ;
        }
        $keyArray=array("id","activity_name","activity_url","activity_status");
        $activityInfo=$this->setArray($keyArray,$activityInfo);
        return $activityInfo;
    }

    /**
     * [advFunction 获取广告]
     * @param [type] $parames [description]
     */
    public function advFunction($parames)
    {
       $condition=[];
       if ($parames['role_flag']==0) 
       {
           $condition['adv_port']=[0,1];
       }elseif ($parames['role_flag']==3) {
           $condition['adv_port']=[0,2];
       }elseif ($parames['role_flag']==4) {
           $condition['adv_port']=[0,3];
       }
       $condition['adv_status']=1;
       $condition['adv_type']=empty($parames['adv_type']) ?1:$parames['adv_type'];
       $advInfo = M_Mysqli_Class('cro', 'AdvModel')->getAdvsInfoByAttr($condition);
        if (empty($advInfo)) 
        {
            return ;
        }
        $keyArray=array("id","adv_url","adv_img_key","adv_img_hash","adv_type","adv_status");
        $t=time();
        $advData=[];//定义空数组存放数据
        foreach ($advInfo as $k => $v) 
        {
            if($v['is_aging']==1)
            {
                if (($t<$v['aging_start'])||($t>$v['aging_end'])) 
                {
                    continue;
                }else{
                    $advData[]=$this->setArray($keyArray,$advInfo[$k]);
                }
            }else{
                $advData[]=$this->setArray($keyArray,$advInfo[$k]);
            }
        }
        unset($advInfo);
        return $advData;
    }
}
?>
