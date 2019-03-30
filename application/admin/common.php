<?php
// +----------------------------------------------------------------------
// | Yershop 开源网店系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.yershop.com All rights reserved.
// +----------------------------------------------------------------------
use think\Db;
use think\AjaxPage;
use think\Loader;
 function get_place($id){
    
         $info0 = db('Category')->find($id);
		 $Lever_1='<a href="'.url('article/lists',array('id'=>$id)).'">'.$info0['title'].'</a>';
		 if(!$info0['pid']){
           return $Lever_1;
		 }
		//2级分类，第2级	 
         $pid=$info0['pid'];
		 $info=db('Category')->where("id='$pid'")->find();
		 //设置链接
		 $Lever_2='<a href="'. url('article/lists',array('id'=>$pid)).'">'.$info['title'].'</a>';
		// 获取当前分类的上级分类主键id
		 $pid2=$info['pid'];
		 if(!$pid2){
            return $Lever_2.">>".$Lever_1;		
		 }
		 $info3=db('Category')->where("id='$pid2'")->find();
		 if(isset($info3)){//判断是否是一级分类,获取标题和标识
			  $info2=db('Category')->where("id='$pid2'")->find();
			 //设置链接
			  $href_3=url('article/lists',array('id'=>$pid2));
			  $Lever_3='<a href="'.$href_3.'">'.$info2['title'].'</a>';
			  return $Lever_3.">>".$Lever_2.">>".$Lever_1;
		 
		 }
	
}
function preg_substr($start, $end, $str) // 正则截取函数       
{       
    $temp = preg_split($start, $str);       
    $content = preg_split($end, $temp[1]);       
    return $content[0];       
}    
function str_substr($start, $end, $str) // 字符串截取函数       
{       
    $temp = explode($start, $str, 2);       
    $content = explode($end, $temp[1], 2);       
    return $content[0];      
} 
/**
 * 权限判断当前id是否在数组里
 * @param int $id   传入id
 * @param array $ids 传入数组
 * @return bool
 */
function is_checked($id ,$ids){
    $arr=explode(",",$ids);
    if(in_array($id,$arr)){
	  return "checked"; 
    }
}
/**
 * 判断是否菜单显示
 * @param int $id   传入要比较的id
 * @param int $pid 传入当前id
 * @return bool
 */
function in_checked($pid,$id){
	if($pid==$id){
		return true; 
	}
    $arr=getParent($pid);
    if(in_array($id,$arr)){
	  return true; 
   }else{
	  return false;  
   }
}
/**
 * 切换风格
 * @return string 
 */
function getStyle(){
    $ISDES=C('ISDES');	
    if($ISDES){ 
	     $msg="v3风格";
     }else{
		 $msg="v2风格";	
     }
	 return $msg;
}



/**
 * 获取配置分组
 * @return array 
 */
function get_config_group(){
   $type   = C("GROUP");
	$type=explode("|",$type);
    return $type;
}
/**
 * 获取属性类型信息
 * @return string  
 */
function get_config_type($type=""){
      $array = array(
         'string'    => '字符串',
         'textarea'  => '文本框',
         'date'      => '日期',
		 'select'     => '枚举',
         'picture'   => '上传图片',
		 'photo'   => '相册',
		 'num'    => '数字',
	     'editor'      => '编辑器', 
		 'color'      => '拾色器', 
		 'file'   =>  '上传附件',
		 'video'   =>  '视频',
       
    );
    return $type?$array[$type]:$array;
}
/**
 * 获取属性类型信息
 * @return string  
 */
function get_attribute_type($type=''){
    // TODO 可以加入系统配置
    static $_type = array(
        'num'       =>  array('数字',"int(10) unsigned NOT NULL DEFAULT '0'"),
        'string'    =>  array('字符串',"varchar(225)  DEFAULT NULL"),
        'textarea'  =>  array('文本框','text NOT NULL'),
        'date'      =>  array('日期',"int(10) unsigned NOT NULL DEFAULT '0'"),
        'datetime'  =>  array('时间',"int(10) unsigned NOT NULL DEFAULT '0'"),
        'bool'      =>  array('布尔',"tinyint(2) NOT NULL DEFAULT '0'"),
        'select'    =>  array('枚举',"char(50)  DEFAULT NULL"),
        'radio'     =>  array('单选',"char(10)  DEFAULT NULL"),
        'checkbox'  =>  array('多选',"varchar(100) NOT NULL DEFAULT '"),
        'editor'    =>  array('编辑器','text NOT NULL'),
        'picture'   =>  array('上传图片',"int(10) unsigned NOT NULL DEFAULT '0'"),
        'file'      =>  array('上传附件',"int(10) unsigned NOT NULL DEFAULT '0'"),
        'photo'   =>  array('上传多图',"varchar(255)  DEFAULT NULL "),
		'video'   =>  array('播放器',"varchar(255)  DEFAULT NULL"),
    );
    return $type?$_type[$type][0]:$_type;
}
/**
 * 获取属性类型信息
 * @return string  
 */
function get_value($key,$array){
  
    return $array[$key];
}
/**
 * 模块的生成url
 * @param int $id   传入的模块id
 * @return string 
 */
function get_moude_url($id){
	 $map['id']=$id;
            // 查找当前子菜单
    $info =Db::name('Module')->where($map)->find();
	$url=$info["url"];
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url =url($url,array('module_id'=>$id));
            break;
    }
    return  $url;
}
/**
 * excel导出
 * @param string $expTitle 导出保存的文件名称
 * @param array  $expCellName 单元格格式
 * @param object $expTableData 查询的数据
 */
function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName ="yershop".date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        //vendor("PHPExcel.PHPMailerAutoload");
		Loader::import('PHPExcel.PHPMailerAutoload');
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]); 
        } 
          // Miscellaneous glyphs, UTF-8   
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
          }             
        }  
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
    }
 /**
 * 分组的生成url	
 * @param int $id   传入的模块id
 * @return string 
 */
function get_group_url($id){
	 $where["id"]=$id; 
	 $info =Db::name('group')->where($where)->order("sort desc")->find();
	 if($info["url"]){
		 $map['url']=$info["url"];   
	 }else{
	   $map['group_id']=$id; 
	   $map['pid']=array("gt",0);
	  }
	  unset($info);
            // 查找当前子菜单
      $info =Db::name('Module')->where($map)->order("sort desc")->find();
	  $url=$info["url"];
	
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url =url($url,array('module_id'=>$info["id"],'group_id'=>$id));
            break;
    }
    return  $url;
}

 /**
 * 获取分组的函数	
 * @param int $id   传入的id
 * @param mixed $field  输出的字段 
 * @return string 
 */
function get_group($id,$field){
	 $map['id']=$id; 
     $info =Db::name('group')->field($field)->where($map)->find();
    return  $info[$field];
}
 /**
 * 获取模块的父id列表	
 * @param int $id   传入的id
 * @return object 
 */
function getimghtml($value,$field){
	   $html="";
	  if($value){
			
             $list= explode(',',$value); 
			  foreach ( $list as  $v ) {
				  $path=get_cover_path($v);
		        $html=$html.'<div class="upload-pre-item"> <img src="'.$path.'"data-id='.$v.'>
						                   <span class="close" onclick="delimage(this,'.$field.');"></span></div>';
		   }
		 }
	
		echo  $html;
	}

 /**
 * 获取模块的父id列表	
 * @param int $id   传入的id
 * @return object 
 */
function getModuleParent($id = 0){
	    $data= Db::name("module")->field("id,pid")->select();
		$arr = array( ); 
		$temp = array();
		   foreach ( $data as $key => $v ) {
		   //判断，如果$v['pid'] == $pid的则压入数组Child
		       if ($v['id'] == $id&&$v['pid']!=0) {
					//递归执行
					  $temp[] = $v['pid']; 
                      $temp = array_merge($temp,getModuleParent($v['pid'],$data));				
		       }
		   }
		return $temp;
	}

 /**
 * 无限分类循环的html格式输出	
 * @param object $data  传入的数据对象
 * @param int $pid  传入的id
 * @param string $html  组合的html格式
 * @param int $level  循环的等级
 * @return string 
 */
 function getSort($data,$pid=0,$html="|——",$level=0){
        $temp = array();
		//$field="id,number,level,pid,username";
		/* 查询条件初始化 */
		//$data=M("UcenterMember")->where($condition)->field($field)->order("id asc")->select();
        foreach ($data as $k => $v) {
            if($v['pid'] == $pid){
                $str = str_repeat($html, $level);
                $v['html'] = $str;
                $temp[] = $v;
                $temp = array_merge($temp,getSort($data,$v['id'],'|——',$level+1));
            }
        }
        return $temp;
 }	
 /**
 * 分析枚举类型配置值 格式 a:名称1,b:名称2	
 * @param string $string  组合的字符串
 * @return string 
 */
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}
/**
 * 分析属性枚举类型配置值 格式 值1:名称1|值2:名称2	
 * @param string $string  组合的字符串
 * @return string 
 */
function parse_attr($string) {
    $array = preg_split('/[|]+/', trim($string, "|"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}