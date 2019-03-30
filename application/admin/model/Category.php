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
/**
 * 分类模型
 */
class Category extends Model{

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
    public function getParentId($id){
      
	    $cid=getParent($id);//	dump($catelist);
		if($cid){
			krsort($cid);
		}
		return $cid;
    }


    public function getChildrenId($id){
      
	    $cid=getChild($id);//	dump($catelist);
		if($cid){
			krsort($cid);
		}
		array_push($cid,$id);
		return $cid;
    }
	//动态获取指定分类商品
    public function getDatalist($cateid){     
        /**分类列表文档**/
        $ids = $this->getChildrenId($cateid);    		
		$map['category_id']=array("in",$ids);   
		$map['status']=1;   
		$list=Db::name( 'Document' )->where($map)->limit(10)->order("id desc")->select();         
		foreach ($list as $k => $v ) {
		   /**重组数据**/
			$id=$v["id"];
			$item[$id]["id"] =$id;    
		    $item[$id]['url'] =U('Article/detail?id='.$id);
            $item[$id]['pic'] =get_cover( $v["cover_id"], "path" );
			$item[$id]['title'] =$v["title"];
			$item[$id]['price'] =$v["price"];
        }
       return $item;
     }
		/**商品分类垂直菜单调用**/
    public function getCategory(){
		$field = 'id,name,pid,title';
		$condition['display']=1;
		$condition['status']=1;
		$condition['ismenu']=1;
		$category = $this->field($field)->order('sort desc')->where($condition)->select( );
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

public function getfooter(){     	
		$map['ismenu']=2;
		$map['pid']=0;
		$map['status']=1;
        $list=$this->where($map)->order( "id asc" )->select( );
		   foreach( $list as $n=> $val ){
			   $list[$n]['id']=$this->where('pid=\''.$val['id'].'\'')->select( );
		   }
       return $list;
     }


}
