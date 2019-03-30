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
class Attributes extends Model{
     protected $auto = ["create_time","update_time","uid"];
     protected $insert = ["create_time","update_time","uid"];  
     protected $update = ["create_time","update_time","uid"];  
     protected function setCreateTimeAttr(){
		   $create_time  = input('create_time');
		   return isset($create_time)?strtotime($create_time):0;
		
    }
	protected function setUidAttr(){
		return is_login();
    }
    protected function setUpdateTimeAttr(){
		
		   $update_time  = input('update_time');
		   return isset($update_time)?strtotime($update_time):0;
		
    }
	/**
     * 新增或更新一个属性
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     */
    public function updates(){
	    $Attributes = new \app\admin\model\Attributes;
        /* 添加或新增属性 */
        if(!$_POST['id']){ //新增属性
                //新增表字段
		   $res=$Attributes->validate(true)->allowField(true)->save($_POST);
		   $error=$Attributes->getError();
		   $id=$Attributes->getLastInsID();
		   if($error){
		     $this->error=$error;
			 return false;
		   }
             if($res){  
				 $res = $this->addColumn($_POST);
                 if(!$res){
                    $Attributes->delete($id);
                    return false;
                 }
                 $map["id"]=$_POST["model_id"];
				 $info=db("models")->where($map)->find();
				 if($_POST["group_id"]=="1"){
				   $str=$info["extend"];
				 }else{
				   $str=$info["base"];
				 }
				 $arr=explode(",",$str);
				 array_push($arr,$id);
				 $arr=array_unique($arr);
                 $str=implode(",",$arr);
                 if($_POST["group_id"]=="1"){
				   $update["extend"]=$str;
				 }else{
				   $update["base"]=$str;
				 }
				 db("models")->where($map)->update($update);
			 }else{
			   return false;
			 }
        } else { 
              //更新表字段
			    $res=$this->updateColumn($_POST);
                if(!$res){ 
					$this->error="字段更新或新增失败";
                    return false;
                }else{
				//更新数据
				   $res=$Attributes->validate(true)->allowField(true)->save($_POST,['id' => $_POST['id']]);
		           $error=$Attributes->getError()?$Attributes->getError():"更新失败";
				   if(!$res){
					 $this->error=$error;
					 return false;
				   }	
				}
        }
        return $_POST;

    }
	 /**
     * 新建表字段
     */
    protected function addColumn($data){
              
		$name =get_models($data["model_id"],"name");	
        $table_name =  config('database.prefix').strtolower($name);
	    $sql = "ALTER TABLE ".$table_name." ADD COLUMN ".$data['name']."  " .$data['value']." COMMENT '" .$data['title']."';" ;
        $res=Db::execute($sql);
        return true;
    }

    /**
     * 更新表字段
     */
    protected function updateColumn($data){
        //获取原字段名
		$map['id']=$data["id"];
            /* 获取数据 */
        $info=db('Attributes')->where($map)->find();        
		$name =get_models($info["model_id"],"name");
		$database=config("database.hostbase");
        $table_name = config('database.prefix').strtolower($name);
		$field=db($name)->find();
        if(!isset($field[$data['name']])){
			$this-> addColumn($data);
		}
        $sql = "ALTER TABLE ".$table_name." CHANGE ".$info["name"]." ".$data['name']."  " .$data['value']." COMMENT '" .$data['title']."';" ;
        $res=Db::execute($sql);
        return true;
    }
	 public function getList($model_id=""){
			 $map["id"]=$model_id;
			 $info=db('models')->where($map)->find();
			 unset($map);
             $array=explode(",",$info["base"]);
			 $arr=array();
             foreach($array as $k=>$v){
			    $map["id"]=$v;  
			    if(db('Attributes')->where($map)->select()){
				  $arr[]=$v;
				}
			 }
             $info["base"]=implode(",",$arr);
			 unset($map);
			 unset($arr);
             if(isset($info["extend"])){
				 $array=explode(",",$info["extend"]);
				 $arr=array();
				 foreach($array as $k=>$v){
					$map["id"]=$v;  
					if(db('Attributes')->where($map)->select()){
					  $arr[]=$v;
					}
				 } 
				 
					 $info["extend"]=implode(",",$arr);
					 
				 
              }else{
			    $info["extend"]=null;
			  }
			  $main=explode(",",$info["base"].','.$info["extend"]);
              $info["base"]=explode(",",$info["base"]);
              $info["extend"]=explode(",",$info["extend"]);
			  $where["model_id"]=1; 
			  $list=db('Attributes')->where($where)->field("id,group_id")->select();
			  foreach($list as $k=>$v){
					$n=$v["id"];  
					if(false==in_array($n, $main)){
					   if($v["group_id"]=="1"){
					    array_push($info["extend"],$n);
					  }else{
					     array_push($info["base"],$n);
					  }
					}
					
			  } 
              $info["base"]=implode(",",$info["base"]);
              $info["extend"]=implode(",",$info["extend"]);
			  $data = array(
							 '0'=>array('group_id'=>'0',"title"=>"基础","fields"=>$info["base"]),  
							 '1'=>array('group_id'=>1,"title"=>"扩展","fields"=>$info["extend"]),    
			  );
			 unset($map);
			 foreach($data as $n=>$v){
					  $map["id"]=array("in",$v["fields"]); 
					  $map["model_id"]=array("in","1,".$model_id);
					  $ids=$v["fields"];
					  if($v["fields"]){
					    $data[$n]["data"]=db('Attributes')->where($map)->order("FIELD(id ,$ids)")->select();
				      }else{
					      $data[$n]["data"]=null;
					  }
			 }
		   

        return $data;
    }
}
