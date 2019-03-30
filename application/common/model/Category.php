<?php
// +----------------------------------------------------------------------
// | Yershop 开源网店系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://yershop.com All rights reserved.
// +----------------------------------------------------------------------
namespace app\common\model;
use think\Model;
use think\Db;
/**
 * 分类模型
 */
class Category extends Model{
    /**
    * 位置，category 模型
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID，传入的分类主键id
     * @param  boolean $field 查询字段,查询所有
     * @return array，返回当前分类及其子分类数据表所有数据，分类树，多维数组
     * @用途供makeTree函数调用
     */
    public function getTree($id=0,$model_id = 0, $field = true){
        /* 获取当前分类信息 */
      

        /* 获取所有商品分类 */
		$map['status']=1;
		$map['type']=1;
		$map['model_id']=$model_id;
        $list =db('category')->field($field)->where($map)->order('sort desc')->select();
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_', $root = $id);
        
        /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }

        return $info;
    }
	 /**
     * 位置，category 模型
     * 构造数据
     * @return array，返回当前分类及其子分类数据表所有数据，分类树，多维数组
     */
   public function makeTree($model_id){
        $category =$this->getTree (0,$model_id);
        foreach ( $category as $k => $v ) {
			$cid=getChild($v["id"]);;
			$arr=array();
			array_push($cid,$v['id']);
			array_push($arr,$v['id']);
             	/**子分类**/
			$category [$k] ['child']= array ();    
			$condition['pid'] = array('in',$arr);
			$category [$k] ['child']= db('category')->where($condition)->limit(10)->order("id desc")->select(); 
			/**分类文章**/
			$category [$k]['item']= array ( );
			$map['category_id']=array("in",$cid);
			
			$category [$k]['item'] =db('document')->where($map)->order("id desc")->limit(20)->select();
			
        }
        return $category;

  }


    /**
     * 获取指定分类子分类ID
     * @param  string $cate 分类ID
     * @return string       id列表
     */
    public function getChildrenId($id){
      
	    $cid=getChild($id);//	dump($catelist);
		if($cid){
			krsort($cid);
		}
		array_push($cid,$id);
		return $cid;
    }
	
    /**
     * 获取指定分类父分类ID
     * @param  string $cate 分类ID
     * @return string       id列表
     */
	 public function getParentId($id){
      
	    $cid=getParent($id);//	dump($catelist);
		if($cid){
			krsort($cid);
		}
		return $cid;
    }
	//动态获取指定分类商品
    public function getDatalist($cateid){     
        /**分类列表文档**/
        $ids = $this->getChildrenId($cateid);    		
		$map['category_id']=array("in",$ids);   
		$map['status']=1;   
		$list=db( 'Document' )->where($map)->limit(10)->order("id desc")->select();         
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
