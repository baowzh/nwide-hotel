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
class Config extends Admin{
	/* 配置首页 */
    public function index(){     
        if($_POST){
			foreach ($_POST as $key=>$value){
				if($value){
					$map[$key]  = array('like', '%'.$value.'%');
				 }
			}
        }
		$map=isset($map)?$map:'';
		$type   = C("GROUP");
		$type=explode("|",$type);
        $list   = db("Config")->where($map)->select();
      
        $this->assign('list',$list);
		$res=getLists('Config',$map,10,'id desc',"");;
	    $this->assign('res', $res);
		$this->meta_title="配置管理";
		$this->assign('meta_title', $this->meta_title);
	    return $this->fetch(); 
	}
	/* 切换布局 */
	public function change(){   
        $ISDES=C('ISDES');	
        if($ISDES){
            $data['value']=0;
        }else{
			$data['value']=1;
		}
		$map['name']="ISDES";
		
		$group=db('Config')->where($map)->update($data);
	    $url=input("server.REQUEST_URI");
		$this->redirect($url); 
	}
	//系统配置
	public function systems($config =""){   
	    if($_POST){
			$res=array();
            foreach ($_POST as $key => $value) {
                $map = array("name" => $key);
				$res=+1;
                db('Config')->where($map)->setField('value', $value);
            }
         
	       if($res){
			     addUserLog("edit_config",session_uid());
		         $this->success("更新成功！");
		   }else{
			   $this->error("更新失败！");
		   } 
	  }
	  else{
		$group  =  input('group');
        $map['group']=$group?$group:0;
		$type   = C("GROUP");
		$type=explode("|",$type);
        $list   = db("Config")->where($map)->select();
        cookie("__forward__",input('server.HTTP_REFERER'));
        $this->assign('list',$list);
        $this->assign('type',$group);//dump($type);
		$this->assign('groups',$type);
        $this->meta_title = '系统设置';
       
		$this->assign('meta_title', $this->meta_title);
	    return $this->fetch();
	   }
	}
    /* 编辑配置 */
	public function edit($id){   
	    if($_POST){
		   $Config= new \app\admin\model\Config;
           $res=$Config->validate(true)->allowField(true)->save($_POST,['id' => $_POST['id']]);
	       if($res){
			     addUserLog("edit_config",session_uid());
		        $this->success("更新成功！",cookie("__forward__"));
		   }else{
			    $error=$Config->getError()?$Ad->getError():"更新失败";
			    $this->error($error);
		   } 
	  }
	  else{
		$group  =  input('group');
        $map['group']=$group?$group:0;
		$type   = C("GROUP");
		$type=explode("|",$type);
         $list   = db("Config")->where($map)->select();
         $this->assign('list',$list);
		 $map2['id']=$id;
		 $info=db("config")->where($map2)->find();
	     $this->assign('info', $info);
		 $this->meta_title="编辑配置"; 
		   cookie("__forward__",input('server.HTTP_REFERER'));
		 $this->assign('meta_title', $this->meta_title);
	     return $this->fetch();
	   }
	}
	/* 增加配置 */
    public function add($id=""){  
	  if($_POST){
		 $Config= new \app\admin\model\Config;
        // 过滤post数组中的非数据表字段数据
           $res=$Config->validate(true)->allowField(true)->save($_POST);
	     if($res){
		     addUserLog("add_config",session_uid());
		     $this->success("新增成功！",cookie("__forward__"));
		 }else{
			$error=$Config->getError()?$Config->getError():"新增失败";
		    $this->error($error);
		 } 
	}
	  else{
		$group  =  input('group');
        $map['group']=$group?$group:0;
		$type   = C("GROUP");
		$type=explode(",",$type);
        $list   = db("Config")->where($map)->select();
      
         $this->assign('list',$list);
		 $this->meta_title="新增配置"; 
		 cookie("__forward__",input('server.HTTP_REFERER'));
		 $this->assign('meta_title', $this->meta_title);
	     return $this->fetch("config/edit");
	  }
	}
	/*删除配置 */
	 public function del(){   
	    $id=input("id");
	    $map['id']=array("in",$id);
		$res=db("config")->where($map)->delete();
		if($res){
		   addUserLog("del_config",session_uid());
		   $this->success("删除成功！");
		}else{
			 $this->success("删除失败！");
		}
	}
	
}
