<?php
// +----------------------------------------------------------------------
// | 贝云cms内容管理系统 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bycms.cn All rights reserved.
// +----------------------------------------------------------------------
// | 版权申明：贝云cms内容管理系统不是一个自由软件，是贝云网络官方推出的商业源码，严禁在未经许可的情况下
// | 拷贝、复制、传播、使用贝云cms内容管理系统的任意代码，如有违反，请立即删除，否则您将面临承担相应
// | 法律责任的风险。如果需要取得官方授权，请联系官方http://www.bycms.cn
// +----------------------------------------------------------------------
namespace app\admin\model;
use think\Model;
use think\Db;
/**
 * 文档基础模型
 */
class Models extends Model{
     protected $auto = ["base","extend","json"];
     protected $insert = ["base","extend","json"];  
     protected $update = ["base","extend","json"];  
     protected function setBaseAttr(){
		  if(input('json/a')){ 
	        return implode(",",$_POST['json'][1]);
	      }else{
		     $map["model_id"]=1;
			 $list=Db::name('Attributes')->where($map)->select();
			 $arr=array();
			 foreach($list as $n=>$v){;
			      array_push($arr,$v["id"]);
			 } 
			 return implode(",",$arr);
		  }
		
    }

	protected function setJsonAttr(){
		   
		  if(input('json/a')){ 
	        return json_encode($_POST['json']);
	      }else{
		     $map["model_id"]=1;
			 $list=Db::name('Attributes')->where($map)->select();
			 $arr=array();
			 foreach($list as $n=>$v){;
			      array_push($arr,$v["id"]);
			 } 
			 return implode(",",$arr);
		  }
		
    }
	 /**
     * 新建表
     */
     public function updatePost(){

          $Models =new \app\admin\Model\Models;
		  if(!$_POST['id']){ //新增属性{
				 $res=$Models->validate(true)->allowField(true)->save($_POST);
				 if(!$res){
					 $error=$Models->getError()?$Models->getError():"新增失败";
					 $this->error=$error;
			         return false;
				 }
				 $table_name =  config('database.prefix').strtolower($_POST['name']);

			 $sql = "CREATE TABLE `".$table_name."` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='".$_POST['title']."表';
			" ;
            $res=Db::execute($sql);

		  }else{
		     $res=$Models->validate(true)->allowField(true)->save($_POST,['id' => $_POST['id']]);
		     if(!$res){
					 $error=$Models->getError()?$Models->getError():"更新失败";
					 $this->error=$error;
			         return false;
			 }
		  }
        return true;
    }
    protected function setExtendAttr(){
		  if(input('json/a')){ 
	        return implode(",",$_POST['json'][2]);
	      }	
		
    }  
}
