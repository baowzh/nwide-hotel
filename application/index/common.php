<?php
use think\Db;
function img($dengji, $dianma){
	$where = array ();
	$where ["等级"] = $dengji;
	$dbConfig = getkefangDBConfig ( $dianma );
	$info = Db::connect ( $dbConfig )->name ( 'tb_djbz' )->where ( $where )->find ();
	$extName = $info ['图片格式'] == null ? 'jpeg' : $info ['图片格式'];
	$extName='jpeg';
	// 已经存在则不写文件
	//$filePath = ROOT_PATH . '/uploads/' . $info ['等级'] . ".jpeg" ;// . $extName;
	$dir=ROOT_PATH . 'uploads\\'.$dianma;
	if (!file_exists($dir)){
		mkdir ($dir,0777,true);
	}
	$filePath=$dir."\\".$info ['等级'] . ".".$extName;
	if (! file_exists ( $filePath )) {
		$txt = $info ['图片'];
		if($txt!=null){
			$file = fopen ( $filePath, 'w' );
			fwrite ( $file, $txt );
		}else{
			return '//pavo.elongstatic.com/i/mobile750_448/nw_000cRACL.jpg';
		}
	}
	return '/uploads/'.$dianma."/" . $info ['等级'] . '.' . $extName;
	
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
 