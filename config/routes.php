<?php
if (!defined('ROOTPATH')) {
    $url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . '/error404';
    header('Location: ' . $url, TRUE, 302);
    exit();
}

return array(
        'default_controller' => 'index',
        '404_override' => 'index',
        'error404' => 'index',
        'login'=>array(
            'post' => 'login/actionPost',
        ),
        'register'=>array(
            'post' => 'register/actionPost',
        ),
        'product'=>array(
            'get' => 'product/actionGet',
            'post' => 'product/actionPost',
        ),
        'usertoken'=>array(
            'get' => 'usertoken/actionGet',
        )
        ,
        'order'=>array(
            'post' => 'order/actionPost',
            'get' => 'order/actionGet'
        ),
        'evaluation'=>array(
            'post' => 'evaluation/actionPost',
            'get' => 'evaluation/actionGet',
            'put' => 'evaluation/actionPut',
            'delete' => 'evaluation/actionDelete'
        ),
        'usercenter'=>array(
            'get' => 'usercenter/actionGet',
            'post' => 'usercenter/actionPost',
            'put' => 'usercenter/actionPut'
        ),
        'fixperfect'=>array(
            'post' => 'fixperfect/actionPost',
            'get' => 'fixperfect/actionGet',
            'put' => 'fixperfect/actionPut'
        ),
        'shopcenter'=>array(
            'get' => 'shopcenter/actionGet',
            'post' => 'shopcenter/actionPost',
            'put' => 'shopcenter/actionPut'
        ),
        'wallet'=>array(
            'post' => 'wallet/actionPost',
            'get' => 'wallet/actionGet'
        ),
        'bankcard'=>array(
            'get' => 'bankcard/actionGet',
            'post' => 'bankcard/actionPost',
            'put' => 'bankcard/actionPut',
            'delete' => 'bankcard/actionDelete'
        ),
         'qiniu'=>array(
            'get' => 'qiniu/actionGet'
        ),
        'shopxg'=>array(
            'get' => 'shopxg/actionGet',
            'post' => 'shopxg/actionPost',
            'put' => 'shopxg/actionPut',
            'delete' => 'shopxg/actionDelete'
        ),
        'feedback'=>array(
            'post' => 'feedback/actionPost'
        ),
        'orderjpush'=>array(
            'post' => 'orderjpush/actionPost',
            'get' => 'orderjpush/actionGet',
        ),
        'orderappeal'=>array(
            'post' => 'orderappeal/actionPost',
            'get' => 'orderappeal/actionGet',
        ),
        'agentcenter'=>array(
            'get' => 'agentcenter/actionGet',
            'post' => 'agentcenter/actionPost',
            'put' => 'agentcenter/actionPut'
        ),
        'agentxg'=>array(
            'get' => 'agentxg/actionGet',
            'post' => 'agentxg/actionPost',
            'put' => 'agentxg/actionPut',
            'delete' => 'agentxg/actionDelete'
        ),
        'fixagent'=>array(
            'get' => 'fixagent/actionGet',
            'post' => 'fixagent/actionPost',
            'put' => 'fixagent/actionPut'
        ),
        'activity'=>array(
            'get' => 'activity/actionGet',
            'post' => 'activity/actionPost',
            'put' => 'activity/actionPut'
        ),
        'insureservice'=>array(
            'get' => 'insureservice/actionGet',
            'post' => 'insureservice/actionPost',
        ),
);