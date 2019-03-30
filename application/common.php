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
use think\Db;
use think\AjaxPage;
/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \think\Verify();
	return $verify->check($code, $id);
}
/**
 * 系统加密函数
 * @param string $str   传入字符串
 * @param string $key 加密密钥
 * @return string
 */
function ucenter_md5($str, $key= 'yercms'){

	return  md5(sha1($str) . $key);
}
 function get_place($id){
    
         $info0 = db('Category')->find($id);
		 $Lever_1='<a href="'.url('goods/lists',array('id'=>$id)).'">'.$info0['title'].'</a>';
		 if(!$info0['pid']){
           return $Lever_1;
		 }
		//2级分类，第2级	 
         $pid=$info0['pid'];
		 $info=db('Category')->where("id='$pid'")->find();
		 //设置链接
		 $Lever_2='<a href="'. url('goods/lists',array('id'=>$pid)).'">'.$info['title'].'</a>';
		// 获取当前分类的上级分类主键id
		 $pid2=$info['pid'];
		 if(!$pid2){
            return $Lever_2.">>".$Lever_1;		
		 }
		 $info3=db('Category')->where("id='$pid2'")->find();
		 if(isset($info3)){//判断是否是一级分类,获取标题和标识
			  $info2=db('Category')->where("id='$pid2'")->find();
			 //设置链接
			  $href_3=url('goods/lists',array('id'=>$pid2));
			  $Lever_3='<a href="'.$href_3.'">'.$info2['title'].'</a>';
			  return $Lever_3.">>".$Lever_2.">>".$Lever_1;
		 
		 }
	
}

/**
 * 时间戳转换普通时间显示
 * @param int $time   传入的时间戳
 * @return string 
 */
function time_format($time=""){
	  return (int)$time?date('Y-m-d H:i:s',(int)$time):''; 
}
/**
 * 商品图片列表
 * @param int $id   传入的商品id
 * @return array
 */
function get_pictures($id){
	$map["id"]=$id;
    $info =Db::name('document')->where($map)->find();
	$list=Db::name('photo')->where($map)->find();
	$covers=explode(",",$list['covers']);
	if($list['covers']){
	      array_push($covers,$info["cover_id"]); 
	}else{
	  $covers=array($info["cover_id"]);
	}
	return $covers;
}
 /**
 * 获取模型的函数	
 * @param int $id   传入的id
 * @param mixed $field  输出的字段 
 * @return string 
 */
function get_models($id,$field){
	 $map['id']=$id; 
     $info =Db::name('models')->field($field)->where($map)->find();
    return  $info[$field];
}
/**
 * 配置读取函数，ot内置函数
 * @param string $name   传入配置英文名称
 * @return string 配置值
 */
function C($name=''){
	 $map['name']=$name; 
     $info =Db::name('config')->where($map)->find();
	 if($info["type"]&&$info["type"]=="picture"){ 
		 $cover_id=$info["value"];
	     $info["value"]=get_cover_path($cover_id); 
	 }
     return  $info["value"];
}
/**
 * 分类字段获取函数
 * @param id $id   传入分类的id
 * @param string $field 字段名称
 * @return mixed
 */
function get_category($id, $field = null){
    if(empty($id)){
        return false;
    }
    $info = Db::name('category')->where(array('id'=>$id))->find();

    return $info[$field];
}  
/**
 * 站点安装根目录
 * @return string
 */
function site_url(){
	$INSTALL_PATH = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']));
    if($INSTALL_PATH==="/"){
          $INSTALL_PATH="";		
    }else{
        $INSTALL_PATH= '/'. trim($INSTALL_PATH,'/').'';
    }
     return  "http://".$_SERVER['HTTP_HOST'].$INSTALL_PATH;
}
/**
 * 域名
 * @return string
 */
function root_url(){
	$INSTALL_PATH = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']));
    if($INSTALL_PATH==="/"){
          $INSTALL_PATH="/";		
    }else{
        $INSTALL_PATH= '/'. trim($INSTALL_PATH,'/').'/';
    }
     return  "http://".$_SERVER['HTTP_HOST'];
}

/**
 * 路由解析读取函数，恢复的tp3.2内置函数
 * @param string $url   传入链接
 * @return string 
 */
function U($url){
     return  url($url);
}
/**
 * 日志函数
 * @param string $title   传入字符串
 * @param string $uid 用户id
 * @return none
 */
function addUserLog($title,$uid="",$type="") {
        $data ['uid'] =$uid?$uid:session_uid();  
		$data ['create_time'] = time ();
		$data ['update_time'] = time ();
		$data ['title'] = $title;
		$data ['type'] = $type?$type:0;
		Db::name ( 'UserLog' )->insert( $data );
}
/**
 * 数量统计函数
 * @param string $model  统计的模型
 * @param string $mark 备注
 * @param int $type 类型
 * @return none
 */
function addCount($model,$mark="",$type="") {
	    if(!$model){
			return false;
		}
		$map["model"]=$model;
		//当天
		$map['time']=date('Ymd', time());
		$info=Db::name('count')->where($map)->count();
		if($info){
			$data["num"]=$info["num"]+1;
		    Db::name('count')->where($map)->update($data);
		}else{
		   $data ['model'] = $model;
		   $data ['num'] = 1;
		   $data ['time']=date('Ymd', time());
		   $data ['create_time']=time();
		   $data ['mark'] = $mark?$mark:0;
		   $data ['type'] = $type?$type:0;
		   Db::name ('count')->insert($data);
		}
		
		
}
/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 地址解析函数，链接url带http返回原值，否则使用url函数解析
 * @param string $url   传入字符串
 * @return string
 */
function get_url($url){
	if(strpos($url,"http")!==false){
		return $url;
	}
    else{
	   return url($url);
    }
}
/**
 * 应用公共文件，获取当前登录模块下的用户id
 * @return int
 */
function session_uid(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return $user['uid'];
    }
}
/**
 * 应用公共文件，获取当前登录模块下的用户id,onethink内置函数
 * @return int
 */
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return $user['uid'];
    }
}
/**
 * 万能字符串过滤函数
 * @param string $url   传入字符串
 * @return string
 */
function safe_replace($string) {
    if(is_array($string)){ 
	   $string=implode('，',$string);
       $string=htmlspecialchars(str_shuffle($string));
	} else{
	    $string=htmlspecialchars($string);
	}
	$string = str_replace('%20','',$string);
    $string = str_replace('%27','',$string);
    $string = str_replace('%2527','',$string);
    $string = str_replace('*','',$string);
    $string = str_replace('"','&quot;',$string);
    $string = str_replace("'",'',$string);
    $string = str_replace('"','',$string);
    $string = str_replace(';','',$string);
    $string = str_replace('<','&lt;',$string);
    $string = str_replace('>','&gt;',$string);
    $string = str_replace("{",'',$string);
    $string = str_replace('}','',$string);
	return $string;
}
/**
 * 应用公共文件，分页函数
 * @param string $model  数据模型名称
 * @param string $map   查询条件
 * @param int $num   分页数量
 * @param string $listsort   排序方式
 * @param string $field   查询的字段
 * @return string
 */
function getLists($model,$map="",$num="10",$listsort="id desc",$field=""){ 
		 $res["list"]=Db::name($model)->where($map)->field($field)->order($listsort)->paginate($num);
		 $res["page"]= $res["list"]->render();
		 $res["count"]=Db::name($model)->where($map)->field($field)->order($listsort)->count();
		return $res;
}
function lists($model,$map="",$num="10",$listsort="id desc",$field=""){ 
		 $list=Db::name($model)->where($map)->field($field)->order($listsort)->paginate($num);
		return $list;
}
function apiLists($model,$map="",$num="10",$listsort="id desc",$p="1",$field=""){ 
		 $res["list"]=Db::name($model)->where($map)->field($field)->order($listsort)->page($p,$num)->select();
		 $res["count"]=Db::name($model)->where($map)->field($field)->order($listsort)->count();
		 $res["pagenum"]= ceil($res["count"]/$num);
		return $res;
}
/**
 * 应用公共文件，ajax分页函数
 * @param string $model  数据模型名称
 * @param string $map   查询条件
 * @param int $num   分页数量
 * @param string $listsort   排序方式
 * @param string $field   查询的字段
 * @return string
 */	
function getAjaxLists($model,$map="",$limitRows="10",$listsort="id desc",$field=""){ 
		  $count=Db::name($model)->where($map)->field($field)->count();
		  $p = new AjaxPage($count, $limitRows,$model); //第三个参数是你需要调用换页的ajax函数名
		  $limit_value = $p->firstRow . "," . $p->listRows;
		  $res["list"]=Db::name($model)->where($map)->field($field)->order($listsort)->limit($limit_value)->select(); // 查询数据
		  $res["page"]=$p->show(); // 产生分页信息，AJAX的连接在此处生成
		  return $res;
}
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}


/**
 * 获取文档封面图片路径
 * @param int $id
 * return string 
 */
function get_cover_path($id){
	
    $info = Db::name('Picture')->where(array('id'=>$id))->find();
	$path=$info["path"];
    return $path?site_url().$path:"";
}
/**
 * 获取文档封面图片路径
 * @param int $id
 * return string 
 */
function get_cover($id){
	
    $info = Db::name('Picture')->where(array('id'=>$id))->find();
	$path=$info["path"];
    return $path?site_url().$path:"__COMMON__/default.png";
}
   /**
     * 获取指定分类的同级分类
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>         
     */
function get_username($uid=null){
    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
        return session('user_auth.username');
    }else{
	   $map['id']=$uid;
	   $info=Db::name("UcenterMember")->where($map)->find();
       return $info['username']; 
	}
}
/**
* 获取用户头像
* @param  int $uid   用户ID
* @return string     
*/
function get_user_cover($uid=null){
  
	   $map['id']=session('user_auth.uid');
	   $info=Db::name("UcenterMember")->where($map)->find();
       return get_cover_path($info['cover_id']); 
	
}
/**
* 字符串截取函数
* @param string $str 传入的字符串
* @param  int $len   截取长度
* @return string     
*/
function substr_cn($str, $len=29) {  
        $str=htmlspecialchars($str);
        $start=0;
        $tmpstr = "";  
        $strlen = $start + $len;  
        for($i = 0; $i < $strlen; $i++){  
            if(ord(substr($str, $i, 1)) > 127){  
                $tmpstr.=substr($str, $i, 3);  
                $i+=2;  
            }else  
                $tmpstr.= substr($str, $i, 1);  
        }  
        return htmlspecialchars($tmpstr);  
 }


 /**
 * 获取分类的父id列表	
 * @param int $id   传入的id
 * @return object 
 */
function getParent($id = 0){
	    $data= Db::name("category")->field("id,pid")->select();
		$arr = array( ); 
		$temp = array();
		   foreach ( $data as $key => $v ) {
		   //判断，如果$v['pid'] == $pid的则压入数组Child
		       if ($v['id'] == $id&&$v['pid']!=0) {
					//递归执行
					  $temp[] = $v['pid']; 
                      $temp = array_merge($temp,getParent($v['pid'],$data));				
		       }
		   }
		return $temp;
	}
 /**
 * 获取分类的子id列表	
 * @param int $id   传入的id
 * @return object 
 */	
	function getChild($id){
	    $data= Db::name("category")->field("id,pid")->select();
		$arr = array( ); 
		$temp = array();
		   foreach ( $data as $key => $v ) {
		   //判断，如果$v['pid'] == $pid的则压入数组Child
		       if ($v['pid'] == $id) {
					//递归执行
					  $temp[] = $v['id']; 
                      $temp = array_merge($temp,getChild($v['id'],$data));				
		       }
		   }
	
		return $temp;
    }