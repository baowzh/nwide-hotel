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
class Category extends Admin{ 
   
    public function index(){     
		$list=$this->getCategory();
	    $this->assign('list', $list);
		$this->meta_title="分类管理";
		$this->assign('meta_title', $this->meta_title);
	    return $this->fetch(); 
	}
	 /* 编辑分类 */
	public function edit($id){   
	    if($_POST){
		   $Category =  new \app\admin\model\Category;
           $res=$Category->validate(true)->allowField(true)->save($_POST,['id' => $_POST['id']]);
	       if($res){
			  addUserLog("edit_Category",session_uid());
		      $this->success("更新成功！",cookie("__forward__"));
		   }else{
			    $error=$Category->getError()?$Category->getError():"更新失败";
			    $this->error($error);
		   } 
	  }
	  else{
		 $map['id']=$id;
		 $info=Db::name("Category")->where($map)->find();
	     $this->assign('info', $info);
		 unset($map);
	     $map['status']=1;
         $field = 'id,pid,title,sort';
		 $list=db( 'Category' )->where($map)->field($field)->order("id asc")->select();
	     $this->assign('list', getSort($list));
		 $model_list=db( 'models' )->order("id asc")->select();
	     $this->assign('model_list', $model_list);
		    cookie("__forward__",input('server.HTTP_REFERER'));
		 $this->meta_title="编辑分类-".$info["title"];
		 $this->assign('meta_title', $this->meta_title);
	     return $this->fetch();
	   }
	}
	 /* 增加分类 */
    public function add($pid=""){  
	    if($_POST){
		   $Category =  new \app\admin\model\Category;
           // 过滤post数组中的非数据表字段数据
           $res=$Category->validate(true)->allowField(true)->save($_POST);
	       if($res){
			 addUserLog("add_Category",session_uid());
		      $this->success("新增成功！",cookie("__forward__"));
		  }else{
			 $error=$Category->getError()?$Category->getError():"新增失败";
			 $this->error($error);
		  } 
	   }
	  else{
		 if($pid){
             $info['pid']=$pid;
			 $this->assign('info', $info);
         }
         $field = 'id,pid,title,sort';
		 $list=db( 'Category' )->field($field)->order("id desc")->select();
	     $this->assign('list', getSort($list));
        
		 $model_list=db( 'models' )->order("id asc")->select();
	     $this->assign('model_list', $model_list);
           cookie("__forward__",input('server.HTTP_REFERER'));
		 $this->meta_title="新增分类";
		 $this->assign('meta_title', $this->meta_title);
	     return $this->fetch("Category/edit");
	  }
	}
	 		/**商品分类菜单调用**/
    public function getCategory(){
	    
			$field = 'id,pid,title,sort,model_id';
			$Category =db( 'Category' )->field($field)->order('sort asc,id asc')->select( );
			$list = $this->unlimitedForLevel($Category);
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
	 /* 删除分类 */
    public function del(){   
	    $id=input("id");
		if($id<10){
		  $this->error("系统数据，禁止删除！");
		}
	    $map['id']=array("in",$id);
		if(!$map["id"]){
			 $this->error("未选择数据！");
		}
		$res=db("Category")->where($map)->delete();
		if($res){
			addUserLog("del_Category",session_uid());
		   $this->success("删除成功！");
		}else{
			 $this->error("删除失败！");
		}
	}
}
