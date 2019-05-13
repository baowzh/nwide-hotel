<?php
use think\Db;
use Think\Log;
function img($dengji, $dianma) {
	$extName = 'jpeg';
	// 已经存在则不写文件
	// $filePath = ROOT_PATH . '/uploads/' . $info ['等级'] . ".jpeg" ;// . $extName;
	$dir = ROOT_PATH . 'uploads\\' . $dianma;
	if (! file_exists ( $dir )) {
		mkdir ( $dir, 0777, true );
	}
	synHouseImgAndVide ( $dengji, $dianma );
	$filePath = $dir . "\\" . $dengji . "." . $extName;
	if (! file_exists ( $filePath )) {
		$dbConfig = getkefangDBConfig ( $dianma );
		$where = array ();
		$where ["等级"] = $dengji;
		$info = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $where )->find ();
		$extName = $info ['图片格式'] == null ? 'jpeg' : $info ['图片格式'];
		//
		$txt = $info ['图片'];
		if ($txt != null) {
			$file = fopen ( $filePath, 'w' );
			fwrite ( $file, $txt );
			fclose($file);
		} else {
			return '//pavo.elongstatic.com/i/mobile750_448/nw_000cRACL.jpg';
		}
	}
	return '/uploads/' . $dianma . "/" . $dengji . '.' . $extName;
}
function video($dengji, $dianma) {
	$extName = 'mp4';
	// 已经存在则不写文件
	// $filePath = ROOT_PATH . '/uploads/' . $info ['等级'] . ".jpeg" ;// . $extName;
	$dir = ROOT_PATH . 'uploads\\' . $dianma;
	if (! file_exists ( $dir )) {
		mkdir ( $dir, 0777, true );
	}
	synHouseImgAndVide ( $dengji, $dianma );
	$filePath = $dir . "\\" . $dengji . "." . $extName;
	if (! file_exists ( $filePath )) {
		//
		$where = array ();
		$where ["等级"] = $dengji;
		$dbConfig = getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $where )->find ();
		$extName = $info ['视频格式'] == null ? 'mp4' : $info ['视频格式'];
		//
		$txt = $info ['视频'];
		if ($txt != null) {
			$file = fopen ( $filePath, 'w' );
			fwrite ( $file, $txt );
			fclose($file);
		} else {
			return '/uploads/demo.mp4';
		}
	}
	return '/uploads/' . $dianma . "/" . $dengji . '.' . $extName;
}
function synHouseImgAndVide($dengji, $dianma) {
	$dbConfig = getkefangDBConfig ( $dianma );
	$where = array ();
	$where ["等级"] = $dengji;
	$info = Db::connect ( $dbConfig )->query ( "select [生成状态] from tb_djwj where [等级]='" . $dengji . "'" );
	if ($info[0]['生成状态'] == 0) {
		// 删除整个目录并重建
		$dir = ROOT_PATH . 'uploads\\' . $dianma."\\";
		if (file_exists ( $dir )) {
			deldir($dir);
			//mkdir ( $dir, 0777, true );
			// 修改 数据库状态
			Db::connect ( $dbConfig )->execute("update tb_djwj set [生成状态]=1 where [等级]='".$dengji."'");
		}
	}
}
function deldir($path) {
	// 如果是目录则继续
	if (is_dir ( $path )) {
		// 扫描一个文件夹内的所有文件夹和文件并返回数组
		$p = scandir ( $path );
		foreach ( $p as $val ) {
			// 排除目录中的.和..
			if ($val != "." && $val != "..") {
				// 如果是目录则递归子目录，继续操作
				if (is_dir ( $path . $val )) {
					// 子目录中操作删除文件夹和文件
					deldir ( $path . $val . '/' );
					// 目录清空后删除空文件夹
					@rmdir ( $path . $val . '/' );
				} else {
					// 如果是文件直接删除
					unlink ( $path . $val );
				}
			}
		}
	}
}
function hotelImg($dianma) {
	$extName = 'jpeg';
	// 已经存在则不写文件
	$dir = ROOT_PATH . 'uploads\\jiudian\\';
	if (! file_exists ( $dir )) {
		mkdir ( $dir, 0777, true );
	}
	synhotelImg($dianma);
	$dir = $dir . '\\'.$dianma.'\\';
	if (! file_exists ( $dir )) {
		mkdir ( $dir, 0777, true );
	}
	
	$filePath = $dir . "\\" . $dianma . "." . $extName;
	if (! file_exists ( $filePath )) {
		//
		$dbConfig = getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_sp' )->find ();
		$extName = $info ['图片格式'] == null ? 'jpeg' : $info ['图片格式'];
		//
		$txt = $info ['图片'];
		if ($txt != null) {
			$file = fopen ( $filePath, 'w' );
			fwrite ( $file, $txt );
		} else {
			return '/public/index/images/jd01.jpg';
		}
	}
	Log::record ('/uploads/jiudian/' . $dianma . "/".$dianma."." . $extName);
	return '/uploads/jiudian/' . $dianma . "/".$dianma."." . $extName;
}
function hotelActiveImg($dianma) {
	$extName = 'jpeg';
	// 已经存在则不写文件
	$dir = ROOT_PATH . 'uploads\\jiudian\\';
	if (! file_exists ( $dir )) {
		mkdir ( $dir, 0777, true );
	}
    synhotelImg($dianma);
	$dir = $dir . '\\'.$dianma.'\\';
	if (! file_exists ( $dir )) {
		mkdir ( $dir, 0777, true );
	}
	$filePath = $dir . "\\" . 'active' . "." . $extName;
	if (! file_exists ( $filePath )) {
		//
		$dbConfig = getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_sp' )->find ();
		$txt = $info ['活动图片'];
		if ($txt != null) {
			$file = fopen ( $filePath, 'w' );
			fwrite ( $file, $txt );
		} else {
			return '/public/index/images/jd01.jpg';
		}
	}
	Log::record ('/uploads/jiudian/' . $dianma . "/active." . $extName);
	return '/uploads/jiudian/' . $dianma . "/active." . $extName;
}
function synhotelImg($dianma) {
	$dbConfig = getkefangDBConfig ( $dianma );
	$info = Db::connect ( $dbConfig )->query ( 'select [生成状态] from tb_sp' );
	//Log::record ($info);
	if ($info[0]['生成状态'] == 0) {
		// 删除整个目录并重建
		$dir = ROOT_PATH . 'uploads\\jiudian\\'. $dianma."\\";
		if (file_exists ( $dir )) {
			deldir($dir);
			//mkdir ( $dir, 0777, true );
			// 修改 数据库状态
			Db::connect ( $dbConfig )->execute("update tb_sp set [生成状态]=1 ");
		}
	}
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
 