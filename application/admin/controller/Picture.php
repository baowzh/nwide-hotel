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
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Picture extends Admin{
    public function index(){     
        if(isset($_GET['title'])){
            $map['title']  = array('like', '%'.(string)input('title').'%');
        }else{
			$map="";
		}
        $res=getLists('Picture',$map,10,'id desc',"");;
	    $this->assign('res', $res);
		$this->meta_title="图片管理";
		$this->assign('meta_title', $this->meta_title);
	    return $this->fetch(); 
	}
	


	public function edit($id){   
	    if($_POST){
		   $Picture= model("Picture");
           $res=$Picture->validate(true)->allowField(true)->save($_POST,['id' => $_POST['id']]);
	       if($res){
			  addUserLog("edit_picture",session_uid());
		      $this->success("更新成功！");
		   }else{
			    $error=$Picture->getError()?$Picture->getError():"更新失败！";
			    $this->error($error);
		   } 
	  }
	  else{
		 $map['id']=$id;
		 $info=db("Picture")->where($map)->find();
	     $this->assign('info', $info);
		
		 $this->meta_title="编辑图片";
		 $this->assign('meta_title', $this->meta_title);
	     return $this->fetch();
	   }
	}
	public function add($id=""){  
	  if($_POST){
		   $Picture= model("Picture");
            // 过滤post数组中的非数据表字段数据
           $res=$Picture->validate(true)->allowField(true)->save($_POST);
	       if($res){
			   addUserLog("add_picture",session_uid());
		       $this->success("新增成功！");
		   }else{
			   $error=$Picture->getError()?$Picture->getError():"新增失败！";
			   $this->error($error);
		   } 
	}
	  else{
		  $map['pid']=0;
	      $list =db('category')->where($map)->select(); 
	      $this->assign('list', $list);
		  $this->meta_title="新增图片";
		  $this->assign('meta_title', $this->meta_title);
	      return $this->fetch("ad/edit");
	  }
	}
	 public function del(){   
	    $id=input("id");
	    $map['id']=array("in",$id);
		if(!$map["id"]){
			 $this->error("未选择数据！");
		}
		$res=db("Picture")->where($map)->delete();
		if($res){
		   addUserLog("del_picture",session_uid());
		   $this->success("删除成功！");
		}else{
			 $this->error("删除失败！");
		}
	}
	
}
