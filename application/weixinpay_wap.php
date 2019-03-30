<?php
/**
 * Created by PhpStorm.
 * User: baowzh
 * Date: 2019/3/30
 * Time: 下午9:16
 */

return[
    'APPID'         =>  "11",       //对应微信公众号APPID
    'MCHID'         =>  "11",       //微信支付商户号
    'NOTIFY_URL'    =>  "http://".$_SERVER['HTTP_HOST']."/index.php/index/notify",     //微信支付回调地址
    'KEY'           =>  ""      //微信支付商户KEY

];