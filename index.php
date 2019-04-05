<?php
// +----------------------------------------------------------------------
// | 贝云cms内容管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bycms.cn All rights reserved.
// +----------------------------------------------------------------------
// | 版权申明：贝云cms内容管理系统不是一个自由软件，是贝云网络官方推出的商业源码，严禁在未经许可的情况下
// | 拷贝、复制、传播、使用贝云cms内容管理系统的任意代码，如有违反，请立即删除，否则您将面临承担相应
// | 法律责任的风险。如果需要取得官方授权，请联系官方http://www.bycms.cn
// +----------------------------------------------------------------------
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');
// [ 应用入口文件 ]
$INSTALL_PATH = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']));

if($INSTALL_PATH==="/"){
    $INSTALL_PATH="/";		
}else{
    $INSTALL_PATH= '/'. trim($INSTALL_PATH,'/').'/';
}
define('INSTALL_PATH',$INSTALL_PATH);//安装目录
define('APP_PATH', __DIR__ . '/application/');
define ( 'BIND_MODULE','index');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
