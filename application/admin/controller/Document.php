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
class Document extends Admin{
    public function index(){     
        if($_POST){
			foreach ($_POST as $key=>$value){
				if($value){
					$map[$key]  = array('like', '%'.$value.'%');
				 }
			}
        }
		$map=isset($map)?$map:'';
		$pid=input('pid');
        if(isset($pid)){
			$Category=new \app\admin\model\Category;
            //当前id及子类列表
			$cate_ids  = $Category->getChildrenId($pid);
			$this->assign("cate_ids",$cate_ids);
			$map["category_id"]=array("in",$cate_ids);
			//当前id
			
        }
		$res=getLists('Document',$map,10,'id desc',"");
	    $this->assign('res', $res);
		$this->meta_title="文章管理";
		$this->assign('meta_title', $this->meta_title);
		$sidebar=db("category")->field("id,title,pid")->order("sort asc,id asc")->select();
		foreach ($sidebar as & $value){
            $value["name"]  =   $value['title'];
			$value["url"]  =url('document/index',array('pid'=>$value["id"]));
        }
        $this->assign('sidebar', json_encode( $sidebar));
	
		$pid=$pid?$pid:161;
		$this->assign("pid",$pid);
	    return $this->fetch(); 
	}

   public function edit($id){   
	    if($_POST){ 
		   $Document = new \app\admin\model\Document;;
		   $res=$Document->updatePost();
	       if($res){
			   addUserLog("edit_document",session_uid());
		      $this->success("更新成功！",cookie("__forward__"));
		   }else{
			   $error=$Document->getError()?$Document->getError():"更新失败！";
			    $this->error($error);
		   } 
	  }
	  else{
		    $map['id']=$id;
            /* 获取数据 */
             $Document=new \app\admin\model\Document;
             $info=$Document->getInfo($id); 
			
             $pid=$info["category_id"];
		     if($pid){
				 $this->assign('pid',$pid); 
			    
             }
			 unset($map);
			 $Attributes=new \app\admin\model\Attributes;
			 $list = $Attributes->getList($info["model_id"]);
             $this->assign('list', $list);
             $sidebar=db("category")->field("id,title,pid")->order("sort asc,id asc")->select();
			 foreach ( $sidebar as &$value){
					$value["name"]  =   $value['title'];
					$value["url"]  =url('document/index',array('pid'=>$value["id"]));
			 }
			 cookie("__forward__",input('server.HTTP_REFERER'));
			 $this->assign('sidebar', json_encode($sidebar));
		     $this->meta_title="编辑文章-".$info["title"];
		     $this->assign('meta_title', $this->meta_title);
			 $this->assign('info', $info);
	         return $this->fetch();
	   }
	}
    public function add($id=""){  
	    if($_POST){
		   $Document = new \app\admin\model\Document;
           // 过滤post数组中的非数据表字段数据
		   $res=$Document->updatePost();
	         if($res){
			  addUserLog("add_document",session_uid());
             $this->success('新增成功',cookie("__forward__"));
		  }else{
			 $error=$Document->getError()?$Document->getError():"新增失败！";
			    $this->error($error);
		  } 
	   }
	  else{
		    $pid=input('pid');
		     if($pid){
				$this->assign('pid',$pid); 
            } 
		    $map["id"]=$pid;
			$cate =db('category')->where($map)->find();
			$this->assign("cate",$cate);
			cookie("__forward__",input('server.HTTP_REFERER'));
			unset($map);
			$sidebar=db("category")->field("id,title,pid")->order("sort asc,id asc")->select();
			foreach ( $sidebar as &$value){
					$value["url"]  =url('document/index',array('pid'=>$value["id"]));
			}
		    $this->assign('sidebar', json_encode($sidebar));
            $Attributes=new \app\admin\model\Attributes;
			$list = $Attributes->getList($cate["model_id"]);
            $this->assign('list', $list);
            $cate_list=$this->getCategory();
	        $this->assign('cate_list', $cate_list);
			$this->meta_title="新增文章"; 
			$info["category_id"]=$pid;
			$this->assign('info', $info);
		    $this->assign('meta_title', $this->meta_title);
	       return $this->fetch();
	  }
	}
	
   //切换分类
    public function change($pid){
	   $field="id,pid,title";
       $pid=input('pid'); // 用intval过滤$_POST['pid']
	   $map["pid"]=$pid; 
	
	   if($data=db("category")->where($map)->field($field)->select()){
		   exit(json_encode($data));
	   }
   }
   public function getCategory(){
	    
		$field = 'id,pid,title,status,sort';
		$category =db( 'category' )->field($field)->order('id asc')->select( );
		$list = $this->unlimitedForLevel($category);
		return $list;
  }
  public function unlimitedForLevel($cate,$name = 'child',$pid = 0){
		$arr = array( );
		foreach ( $cate as $key => $v ) {
		//判断，如果$v['pid'] == $pid的则压入数组Child
			if ($v['pid'] == $pid) {
				//递归执行
				$v[$name] = self::unlimitedForLevel($cate,$name,$v['id']);
				$arr[] = $v;
			}
		}
		return $arr;
	}

	public function del(){   
	    $id=input("id");
		$Document = new \app\admin\model\Document;
	    $map['id']=array("in",$id);
		if(!$map["id"]){
			 $this->error("未选择数据！");
		}
		$res=$Document->del($id);
		if($res){
			 addUserLog("del_document",session_uid());
		   $this->success("删除成功！");
		}else{
			 $this->error("删除失败！");
		}
	}

}