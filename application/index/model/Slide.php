<?php
// +----------------------------------------------------------------------
// | yercms,yershop
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.yershop.com All rights reserved.
// +----------------------------------------------------------------------
namespace app\index\model;
use think\Model;
use think\Db;
/**
 * 幻灯片模型
 */
class Slide extends Model{

    
 
public function getdata($map="",$order=""){     	
        $list=Db::name("slide")->where($map)->order($order)->select();
        return $list;
     }
}
