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
 * 分类模型
 */
class Document extends Model{
  
     protected $auto = ["create_time","update_time","uid"];
     protected $insert = ["create_time","update_time","uid"];  
     protected $update = ["create_time","update_time","uid"];  
     protected function setCreateTimeAttr(){
		   $create_time  = input('create_time');
		   return isset($create_time)?strtotime($create_time):time();
		
    }
	protected function setUidAttr(){
		return is_login();
    }
    protected function setUpdateTimeAttr(){
		
		   $update_time  = input('update_time');
		   return time();
		
    }
    /**
     * 获取详情页数据
     * @param  integer $id 文档ID
     * @return array       详细数据
     */
    public function getInfo($id){
        /* 获取基础数据 */
	
		$map["id"]=$id;
        $info =db("Document")->where($map)->field(true)->find();
        if(!(is_array($info) || 1 !== $info['status'])){
            $this->error('文档被禁用或已删除！');
            //return false;
        }	
		
        /* 获取模型数据 */
		$name  =get_models($info["model_id"],'name');
		$merge=db($name)->find($id);
        if($merge){
			$info = array_merge($info, $merge);
		}
        return $info;
    }
    public function del($id){
        /* 获取基础数据 */
	
		$map['id']=array("in",$id);
        $info =db("Document")->where($map)->field(true)->find();
        if(!(is_array($info) || 1 !== $info['status'])){
            $this->error('文档被禁用或已删除！');
            //return false;
        }	
		db("Document")->where($map)->field(true)->delete();
        /* 获取模型数据 */
		$name  =get_models($info["model_id"],'name');
		$merge=db($name)->where($map)->delete();
      
        return $info;
    }

    /**
     * 新增或更新一个文档
     */
    public function updatePost($id = null){
          $Document =new \app\admin\Model\Document;
		  if(!$_POST['id']){ //新增属性{
				 $res=$Document->validate(true)->allowField(true)->save($_POST);
				 if(!$res){
					 $error=$Document->getError()?$Document->getError():"新增失败";
					 $this->error=$error;
			         //return false;
				 }
				 $id= $Document->getLastInsID();

		  }else{
		     $res=$Document->validate(true)->allowField(true)->save($_POST,['id' => $_POST['id']]);
		     if(!$res){
					 $error=$Document->getError()?$Document->getError():"更新失败";
					 $this->error=$error;
			         //return false;
			 }
	     }
        /* 添加或新增扩展内容 */
        $class = $this->getClass($_POST['model_id']);
		$type=get_models($_POST['model_id'],'type');
		if($type===1){
        if ($id) {//新增数据 
			$_POST['id'] = $id;
            $res =$class->allowField(true)->save($_POST);
            if (!$res) {
				 $map["id"]=$id;
				 db("Document")->delete($id);
                 $this->error = '新增数据失败！';
                 return false;
            }
        } else { //更新数据
            $status = $class->allowField(true)->save($_POST,['id' =>$_POST['id']]);
            if (false === $status) {
                $this->error = '更新数据失败！';
                return false;
            }
         }
		}
        return true;
		
    }

    /**
     * 获取扩展模型对象
     * @param  integer $model 模型编号
     * @return object         模型对象
     */
    public function getClass($id){
        $name  =get_models($id,'name');
		$class=substr($name,0,1);
		$name=strtoupper($class).substr($name,1);
        $class =  'app\admin\model\\' . $name;
        return new $class;
    }

    
}
