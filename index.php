<?php
date_default_timezone_set('Asia/Shanghai');
define('ROOTPATH', dirname(dirname(__FILE__)) . '/');
require_once ROOTPATH . '/cro_config/define.php';
$d = new DefineConfig('xiuapi');
define('APP_NAME', 'cro_xiuapi');
define('DOMAIN_HOST', 'xiu.bike');
$d->bootstrap();
require_once BASEPATH . DS . ENVIRONMENT . DS . 'CodeIgniter.php';
