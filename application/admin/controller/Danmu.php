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
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Danmu extends Admin{
	/* text——弹幕文本内容。

color——弹幕颜色。

position——弹幕位置 0为滚动 1 为顶部 2为底部

size——弹幕文字大小。 0为小字 1为大字

time——弹幕所出现的时间。 单位为分秒（十分之一秒）

isnew——当出现该属性时（属性值可为任意），会认为这是用户新发的弹幕，从而弹幕在显示的时候会有边框。
*/
	
	public function getData(){     

	    $id=input('id'); 	
        if(!($id && is_numeric($id))){
		   $this->error('ID错误！');
		}else{
		   $where["id"]=$id;
		}
	    $info= Db::name('document')->where($where)->find();
		if(!$info){
		    $this->error('文章不存在！');
		} 
		
		$map["doc_id"]=$id;
		$res=db("danmu")->where($map)->field("text,color,size,position,time")->select();
	
		if($res){
		      echo json_encode($res);
		}else{
			  $error="发布失败！";
			  $this->error($error);
		} 
	
	}

	public function sendData(){     
      if(!is_login()){
			//$this->error("请先登录",'user/login');
		}
	    $id=input('id'); 	
        if(!($id && is_numeric($id))){
		   $this->error('ID错误！');
		}else{
		   $where["id"]=$id;
		}
	    $info= Db::name('document')->where($where)->find();
		if(!$info){
		    $this->error('文章不存在！');
		} 
		$js=json_decode($_POST['danmu']);
        $data["text"]=$js->text;
		if($this->check($data["text"])){
		    $this->error("发布失败！");
		}
		$data["color"]=$js->color;
		$data["size"]=$js->size;
		$data["position"]=$js->position;
        $data["create_time"]=time();
		$data["doc_id"]=$id;
		$res=db("danmu")->insert($data);
		if($res){
		      $this->success("发布成功！");
		}else{
			  $error="发布失败！";
			  $this->error($error);
		} 
	
	}

	public function check($str){     

	    $allergicWord = array('法轮功','骂人话');$info="";  
		for ($i=0;$i<count($allergicWord);$i++){  
			$content = substr_count($str, $allergicWord[$i]);  
			if($content>0){  
				$info = $content;  
				break;  
			 }  
		}  
		if($info>0){  
		   //有违法字符   
		   return TRUE;  
		}else{  
		   //没有违法字符  
		   return FALSE;  
		} 
	
	}
	
}
