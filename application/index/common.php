<?php
use think\Db;
function img($dengji, $dianma){
	
	$extName='jpeg';
	// 已经存在则不写文件
	//$filePath = ROOT_PATH . '/uploads/' . $info ['等级'] . ".jpeg" ;// . $extName;
	$dir=ROOT_PATH . 'uploads\\'.$dianma;
	if (!file_exists($dir)){
		mkdir ($dir,0777,true);
	}
	$filePath=$dir."\\".$dengji . ".".$extName;
	if (! file_exists ( $filePath )) {
		//
		$where = array ();
		$where ["等级"] = $dengji;
		$dbConfig = getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $where )->find ();
		$extName = $info ['图片格式'] == null ? 'jpeg' : $info ['图片格式'];
		//
		$txt = $info ['图片'];
		if($txt!=null){
			$file = fopen ( $filePath, 'w' );
			fwrite ( $file, $txt );
		}else{
			return '//pavo.elongstatic.com/i/mobile750_448/nw_000cRACL.jpg';
		}
	}
	return '/uploads/'.$dianma."/" . $dengji . '.' . $extName;
	
}

function video($dengji, $dianma){

	$extName='mp4';
	// 已经存在则不写文件
	//$filePath = ROOT_PATH . '/uploads/' . $info ['等级'] . ".jpeg" ;// . $extName;
	$dir=ROOT_PATH . 'uploads\\'.$dianma;
	if (!file_exists($dir)){
		mkdir ($dir,0777,true);
	}
	$filePath=$dir."\\".$dengji . ".".$extName;
	if (! file_exists ( $filePath )) {
		//
		$where = array ();
		$where ["等级"] = $dengji;
		$dbConfig = getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $where )->find ();
		$extName = $info ['视频格式'] == null ? 'mp4' : $info ['视频格式'];
		//
		$txt = $info ['视频'];
		if($txt!=null){
			$file = fopen ( $filePath, 'w' );
			fwrite ( $file, $txt );
		}else{
			return '/uploads/demo.mp4';
		}
	}
	return '/uploads/'.$dianma."/" . $dengji . '.' . $extName;

}





function hotelImg($dianma){
	$extName='jpeg';
	// 已经存在则不写文件
	$dir=ROOT_PATH . 'uploads\\jiudian\\';
	if (!file_exists($dir)){
		mkdir ($dir,0777,true);
	}
	$filePath=$dir."\\".$dianma . ".".$extName;
	if (! file_exists ( $filePath )) {
		//
		$dbConfig = getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_sp' )->find ();
		$extName = $info ['图片格式'] == null ? 'jpeg' : $info ['图片格式'];
		//
		$txt = $info ['图片'];
		if($txt!=null){
			$file = fopen ( $filePath, 'w' );
			fwrite ( $file, $txt );
		}else{
			return '/public/index/images/jd01.jpg';
		}
	}
	return '/uploads/jiudian/'.$dianma.".". $extName;

}

function hotelActiveImg($dianma){

	$extName='jpeg';
	// 已经存在则不写文件
	$dir=ROOT_PATH . 'uploads\\active\\';
	if (!file_exists($dir)){
		mkdir ($dir,0777,true);
	}

	$filePath=$dir."\\".$dianma . ".".$extName;
	if (! file_exists ( $filePath )) {
		//
		$dbConfig = getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_sp' )->find ();
		//$extName = $info ['图片格式'] == null ? 'jpeg' : $info ['图片格式'];
		//
		$txt = $info ['活动图片'];
		if($txt!=null){
			$file = fopen ( $filePath, 'w' );
			fwrite ( $file, $txt );
		}else{
			return '/public/index/images/jd01.jpg';
		}
	}
	return '/uploads/active/'.$dianma.".". $extName;

}


 function getkefangDBConfig($dianma) {
	$condition = array ();
	$condition ['店码'] = $dianma;
	$shopInfo = Db::name ( 'tb_shop' )->where ( $condition )->find ();
	$dbConfig = array ();
	$dbType = config ( 'config_db' )['type'];
	$dbConfig ['type'] = $dbType;
	$dbConfig ['hostname'] = trim ( $shopInfo ['服务器'], "\r" );
	$dbConfig ['database'] = trim ( $shopInfo ['数据库'], "\r" );
	$dbConfig ['username'] = trim ( $shopInfo ['用户'], "\r" );
	$dbConfig ['password'] = trim ( $shopInfo ['密码'], "\r" );
	// $dbConfig['hostport']=config ( 'config_db' )['hostport'];
	return $dbConfig;
}
 