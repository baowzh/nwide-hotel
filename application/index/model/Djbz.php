<?php

namespace app\index\model;

use think\Model;
use think\Db;
use Think\Log;

/**
 * 文档基础模型
 */
class Djbz extends Model {
	private $zhToEnMap = [ 
			"等级" => "dengji",
			"名称" => "mingcheng",
			"描述" => 'miaoshu',
			'包房价' => 'baofangjia',
			'床位价' => 'chuangweijia',
			'钟点价' => 'zhongdianjia',
			'钟点率' => 'zhongdianlv',
			'打折限度' => 'dazhexiandu',
			'床数' => 'chuangshu',
			'钟点时限' => 'zhongdianshixian',
			'执行日期' => 'zhixingriqi',
			'钟点小时' => 'zhongdianxiaoshi',
			'服务提成' => 'fuwuticheng',
			'序号' => 'xuhao',
			'网订价' => 'wangdingjia',
			'网会价' => 'wanghuijia',
			'预定金额' => 'yudingjine',
			'图片' => 'tupian',
			'图片格式' => 'tupiangeshi',
			'视频' => 'shipin',
			'视频格式' => 'shipingeshi',
			'可售' => 'keshou' ,
			'说明'=>'shuoming'
	];
	private $enTozhMap = [ 
			"dengji" => '等级',
			"mingcheng" => "名称",
			'miaoshu' => "描述",
			'baofangjia' => '包房价',
			'chuangweijia' => '床位价',
			'zhongdianjia' => '钟点价',
			'zhongdianlv' => '钟点率',
			'dazhexiandu' => '打折限度',
			'chuangshu' => '床数',
			'zhongdianshixian' => '钟点时限',
			'zhixingriqi' => '执行日期',
			'zhongdianxiaoshi' => '钟点小时',
			'fuwuticheng' => '服务提成',
			'xuhao' => '序号',
			'wangdingjia' => '网订价',
			'wanghuijia' => '网会价',
			'yudingjine' => '预定金额',
			'tupian' => '图片',
			'tupiangeshi' => '图片格式',
			'shipin' => '视频',
			'shipingeshi' => '视频格式',
			'keshou' => '可售' ,
			'shuoming'=>'说明'
	];
	public function index() {
		$shopInfos = Db::name ( 'tb_shop' )->select ();
		return $shopInfos;
	}
	public function listByCatelog($dianma, $dengji) {
		$dbConfig = $this->getkefangDBConfig ( $dianma );
		// Log::record ($dbConfig );
		$result = array ();
		// 是否可以预定？
		$dbType = config ( 'config_db' )['type'];
		if ($dbType == 'mysql') {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $dengji . "'  " );
		} else if ($dbType == 'sqlsrv') {
			$kefngList = Db::connect ( $dbConfig )->query ( "select distinct 楼号, 房号, 房态,  RTRIM(LTRIM(净)) AS 净
					  from tvhk where [等级]='" . $dengji . "'  " );
		} else {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $dengji . "'  " );
		}
		
		// 按8个一单位机型分组
		$kefngArrays = array ();
		$row = array ();
		$put = false;
		$index_bak = 0;
		for($index = 0; $index < sizeof ( $kefngList ); $index ++) {
			$put = false;
			array_push ( $row, $kefngList [$index] );
			if (($index_bak + 1) % 8 == 0) {
				if (sizeof ( $row ) > 0) {
					array_push ( $kefngArrays, $row );
					// Log::record ($row);
					$row = array ();
					$put = true;
				}
			}
			
			$index_bak ++;
		}
		if (! $put && sizeof ( $row ) > 0) {
			array_push ( $kefngArrays, $row );
		}
		// Log::record ($kefngArrays );
		$result ['kefngList'] = $kefngArrays;
		$result ['vo'] = $this->findDJbz ( $dengji, $dianma )['info'];
		return $result;
	}
	/**
	 * 查询客房信息并把汉字列转换为拼音列返回
	 *
	 * @var arry[array]
	 */
	public function query($dianma) {
		//
		$condition = array ();
		$condition ['店码'] = $dianma;
		$shopInfo = Db::name ( 'tb_shop' )->where ( $condition )->find ();
		$dbConfig = $this->getkefangDBConfig ( $dianma );
		$djCondition=array();
		$djCondition['可售']=1;
		$list = Db::connect ( $dbConfig )->table('tb_djwj' )->where($djCondition)->order ( '序号' )->select ();
		$convertedValues = array ();
		foreach ( $list as $rowValue ) {
			$row = array ();
			foreach ( $this->zhToEnMap as $field ) {
				$fieldName = $this->enTozhMap [$field];
				$fieldValue = $rowValue [$fieldName];
				$row [$field] = $fieldValue;
			}
			array_push ( $convertedValues, $row );
		}
		// 获取每个房间类型可订购的房间数
		$dbConfig = $this->getkefangDBConfig ( $dianma );
		$retrunValues = array ();
		foreach ( $convertedValues as $rowValue ) {
			//
			$dbType = config ( 'vip_db' )['type'];
			if ($dbType == 'mysql') {
				$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $rowValue ['dengji'] . "' and ( `房态`= 'A' and `净`='') " );
			} else if ($dbType == 'sqlsrv') {
				$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where [等级]='" . $rowValue ['dengji'] . "' and ( [房态]= 'A' and [净]='') " );
			} else {
				$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $rowValue ['dengji'] . "' and ( `房态`= 'A' and `净`='') " );
			}
			$rowValue ['count'] = sizeof ( $kefngList );
			// Log::record ( $rowValue );
			array_push ( $retrunValues, $rowValue );
		}
		// Log::record ( $retrunValues );
		return $retrunValues;
	}
	/**
	 * 获取客房数据库配置
	 *
	 * @param unknown $dianma        	
	 * @return multitype:NULL unknown
	 */
	private function getkefangDBConfig($dianma) {
		$condition = array ();
		$condition ['店码'] = $dianma;
		$shopInfo = Db::name ( 'tb_shop' )->where ( $condition )->find ();
		Log::record ( $shopInfo );
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
	
	/**
	 * 获取vip 库连接串
	 *
	 * @param unknown $dianma        	
	 * @return multitype:NULL unknown
	 */
	private function getVIPDBConfig($dianma) {
		$condition = array ();
		$condition ['店码'] = $dianma;
		$shopInfo = Db::name ( 'tb_shop' )->where ( $condition )->find ();
		$dbConfig = array ();
		$dbType = config ( 'config_db' )['type'];
		$dbConfig ['type'] = $dbType;
		$dbConfig ['hostname'] = trim ( $shopInfo ['会服务器'], "\r" );
		$dbConfig ['database'] = trim ( $shopInfo ['会数据库'], "\r" );
		$dbConfig ['username'] = trim ( $shopInfo ['会用户'], "\r" );
		$dbConfig ['password'] = trim ( $shopInfo ['会密码'], "\r" );
		// $dbConfig['hostport']=config ( 'config_db' )['hostport'];
		return $dbConfig;
	}
	
	/**
	 * 生成查看的视频文件并返回url
	 *
	 * @param unknown $dengji        	
	 * @return string
	 */
	public function getVideo($dengji, $dianma) {
		$where = array ();
		$where ["等级"] = $dengji;
		$dbConfig = $this->getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $where )->find ();
		// 已经存在视频的话不写文件
		$extName = $info ['视频格式'] == null ? 'mp4' : $info ['视频格式'];
		$filePath = ROOT_PATH . '/uploads/' . $info ['等级'] . '.' . $extName;
		if (! file_exists ( $filePath )) {
			$file = fopen ( ROOT_PATH . '/uploads/' . $info ['等级'] . '.' . $extName, 'w' );
			$txt = $info ['视频'];
			// 16进制转为2进制
			$txt = hex2bin ( $txt );
			fwrite ( $file, $txt );
		}
		return '/uploads/' . $info ['等级'] . '.' . $extName;
	}
	/**
	 * 生成客房图片并写本地磁盘
	 *
	 * @param unknown $dengji        	
	 * @return string
	 */
	public function getImage($dengji, $dianma) {
		$where = array ();
		$where ["等级"] = $dengji;
		$dbConfig = $this->getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $where )->find ();
		$extName = $info ['图片格式'] == null ? 'jpeg' : $info ['图片格式'];
		$extName = 'jpeg';
		// 已经存在则不写文件
		// $filePath = ROOT_PATH . '/uploads/' . $info ['等级'] . ".jpeg" ;// . $extName;
		$dir = ROOT_PATH . 'uploads\\' . $dianma;
		if (! file_exists ( $dir )) {
			mkdir ( $dir, 0777, true );
		}
		$filePath = $dir . "\\" . $info ['等级'] . "." . $extName;
		if (! file_exists ( $filePath )) {
			$file = fopen ( $filePath, 'w' );
			$txt = $info ['图片'];
			Log::record ( $txt );
			fwrite ( $file, $txt );
		}
		return '/uploads/' . $dianma . "/" . $info ['等级'] . '.' . $extName;
	}
	/**
	 * 订购房间并生成微信支付订单信息
	 */
	public function order($orderForm) {
		$result = array ();
		$kefangCondition = array ();
		$dbConfig = $this->getkefangDBConfig ( $orderForm ['dianma'] );
		$kefangCondition ["等级"] = $orderForm ['dengji'];
		$kefangInfo = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $kefangCondition )->find ();
		// 是否可以预定？
		$dbType = config ( 'vip_db' )['type'];
		if ($dbType == 'mysql') {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' and `净`='') " );
		} else if ($dbType == 'sqlsrv') {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where [等级]='" . $orderForm ['dengji'] . "' and ( [房态]= 'A' and [净]='') " );
		} else {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' and `净`='') " );
		}
		$rooms = explode ( ",", $orderForm ['rooms'] );
		$roomCount = sizeof ( $rooms );
		if (sizeof ( $kefngList ) < $roomCount) {
			$result ['success'] = false;
			$result ['mess'] = '房间数不够';
			return $result;
		}
		// 校验每个房间的状态
		foreach ( $rooms as $rowValue ) {
			$dbType = config ( 'vip_db' )['type'];
			if ($dbType == 'mysql') {
				$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' and `净`='')  and '房号'='" . $rowValue . "'" );
			} else if ($dbType == 'sqlsrv') {
				Log::record ( $rowValue );
				// Log::record ( "select * from tvhk where [等级]='" . $orderForm ['dengji'] . "' and ( [房态]= 'A' and [净]='') and [房号]='" . $rowValue . "'" );
				$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where [等级]='" . $orderForm ['dengji'] . "' and ( [房态]= 'A' and [净]='') and [房号]='" . $rowValue . "'" );
			} else {
				$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' and `净`='') and '房号'='" . $rowValue . "'" );
			}
			if ($kefngList == null || sizeof ( $kefngList ) == 0) {
				$result ['success'] = false;
				$result ['rowValue'] = $rowValue;
				$result ['mess'] = '房间' . $rowValue . '已被预订，请选择其他房间';
				return $result;
			}
		}
		
		//
		// 2.查询是否为会员
		$vipCondition = [ 
				'证件号码' => '',
				'证件类型' => '' 
		];
		$vipCondition ['证件号码'] = $orderForm ['shenfenzhenghao'];
		$vipCondition ['证件类型'] = 1;
		$vipInfo = Db::connect ( $this->getVIPDBConfig ( $orderForm ['dianma'] ) )->name ( 'tb_huiy' )->where ( $vipCondition )->select ();
		$isVip = false;
		if ($vipInfo != null && ! empty ( $vipInfo )) {
			$isVip = true;
		} else {
			$isVip = false;
		}
		$orderForm ['deposit'] = $kefangInfo ['预定金额'];
		// 如果是 会员用什么价格去计算？
		if ($isVip) {
			$orderForm ['deposit'] = $kefangInfo ['预定金额'];
		}
		
		$shopCondition = [ 
				'店码' => config ( 'shop_id' ) 
		];
		// 3.查询微信支付相关参数
		$shopInfo = Db::name ( 'tb_shop' )->where ( $shopCondition )->find ();
		if ($shopInfo != null && ! empty ( $shopInfo )) {
			$orderForm ['dianma'] = $shopInfo ['店码'];
			$orderForm ['dianming'] = $shopInfo ['店名'];
		} else {
			return [ 
					'success' => false,
					'mess' => '未找到支付相关参数设置' 
			];
		}
		
		// 4.生成订单
		$orderForm ['kefngList'] = $kefngList;
		$orderForm ['roomCount'] = $roomCount;
		$orderInfo = $this->createInnerOrder ( $orderForm );
		
		$order = array (
				'body' => '宾馆客房预订资金', // 商品描述（需要根据自己的业务修改）
				'total_fee' => $orderInfo ['money'], // 订单金额 以(分)为单位（需要根据自己的业务修改）
				'out_trade_no' => $orderInfo ['dingdanhao'], // 订单号（需要根据自己的业务修改）
				'product_id' => $orderInfo ['dingdanhao'] 
		); // 商品id（需要根据自己的业务修改）
		
		return [ 
				'success' => true,
				'mess' => '成功',
				'data' => $order 
		];
		
		// return $order;
	}
	public function findDJbz($dengji, $dianma) {
		$where = array ();
		$where ["等级"] = $dengji;
		$dbConfig = $this->getkefangDBConfig ( $dianma );
		$info = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $where )->find ();
		$condition = array ();
		$condition ['店码'] = $dianma;
		$shopInfo = Db::name ( 'tb_shop' )->where ( $condition )->find ();
		$result = array ();
		$result ['info'] = $info;
		$result ['shopInfo'] = $shopInfo;
		return $result;
	}
	/**
	 * 微信支付回调接口，在这里面做订房操作
	 *
	 * @param unknown $orderId        	
	 */
	public function notify($orderId,$transactionId) {
		// 查看订单表 如果有未支付的订单则
		$result = array ();
		$condition = array ();
		$condition ['dingdanhao'] = $orderId;
		$orderInfo = Db::name ( 'hotelorder' )->where ( $condition )->find ();
		if ($orderInfo == null || empty ( $orderInfo )) {
			$result ['success'] = false;
			return $result;
		}
		if ($orderInfo ['status'] == 1) {
			$result ['success'] = true;
			return $result;
		}
		
		// 查询班次
		$dbConfig = $this->getkefangDBConfig ( $orderInfo ['dianma'] );
		
		$banci = Db::connect ( $dbConfig )->query ( "select max('班次') from tbanci" );
		
		$dbType = config ( 'vip_db' )['type'];
		if ($dbType == 'mysql') {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' and `净`='') " );
		} else if ($dbType == 'sqlsrv') {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where [等级]='" . $orderInfo ['dengji'] . "' and ( [房态]= 'A' and [净]='') " );
		} else {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' and `净`='') " );
		}
		// 生成客房订单
		$yudinghao = $orderInfo ['yudinghao'];
		$yudinghaos = explode ( ",", $yudinghao );
		$yudingshu = sizeof ( $yudinghaos );
		if (sizeof ( $kefngList ) < $yudingshu) {
			$result ['success'] = false;
			$result ['mess'] = '房间数不够';
			return $result;
		}
		
		foreach ( $yudinghaos as $rowValue ) {
			$dbType = config ( 'vip_db' )['type'];
			if ($dbType == 'mysql') {
				$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' and `净`='')  and '房号'='" . $rowValue . "'" );
			} else if ($dbType == 'sqlsrv') {
				// Log::record ( $orderInfo );
				// Log::record ( $rowValue );
				$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where [等级]='" . $orderInfo ['dengji'] . "' and ( [房态]= 'A' and [净]='') and [房号]='" . $rowValue . "'" );
			} else {
				$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' and `净`='') and '房号'='" . $rowValue . "'" );
			}
			if ($kefngList == null || sizeof ( $kefngList ) == 0) {
				$result ['success'] = false;
				$result ['mess'] = '房间' . $rowValue . '已被预订，请选择其他房间';
				return $result;
			}
		}
		//
		
		$vipCondition = array ();
		$vipCondition ['证件号码'] = $orderInfo ['shenfenzhenghao'];
		$vipCondition ['证件类型'] = 1;
		$vipInfo = Db::connect ( $this->getVIPDBConfig ( $orderInfo ['dianma'] ) )->name ( 'tb_huiy' )->where ( $vipCondition )->select ();
		$isVip = false;
		if ($vipInfo != null && ! empty ( $vipInfo )) {
			$isVip = true;
		} else {
			$isVip = false;
		}
		//
		$kefangCondition = array ();
		$kefangCondition ["等级"] = $orderInfo ['dengji'];
		$roomTypeInfo = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $kefangCondition )->find ();
		
		foreach ( $yudinghaos as $rowValue ) {
			// $kefangInfo = $kefngList [$index];
			// print_r($orderInfo);
			// 修改为预定
			$updateFields = array ();
			$updCondition = array ();
			$updCondition ['房号'] = $rowValue;
			$updateFields ['姓名'] = $orderInfo ['xingming'];
			/*
			 * $updateFields ['性别'] = $orderInfo ['xingbie'];
			 * $updateFields ['证件'] = '身份证';
			 * $updateFields ['证件编号'] = $orderInfo ['shenfenzhenghao'];
			 * $updateFields ['入住天数'] = $orderInfo ['tianshu'];
			 * $updateFields ['入住日期'] = date ( 'Y-m-d H:i:s' );
			 * $updateFields ['宾客电话'] = $orderInfo ['dianhua'];
			 * $updateFields ['宾客类别'] = '网订';
			 * $updateFields ['网订'] = '是';
			 * // $updateFields['网订']='是';
			 * $updateFields ['房态'] = 'E';
			 */
			
			if ($isVip) {
				$updateFields ['登记房价']= $roomTypeInfo ['网会价'];
				$updateFields ['预交押金'] = $roomTypeInfo ['预定金额'];
			} else {
				$updateFields ['登记房价']= $roomTypeInfo ['网订价'];
				$updateFields ['预交押金'] = $roomTypeInfo ['预定金额'];
			}
			// $updateFields ['网付流水'] = $orderInfo ['dingdanhao'];
			// if ($banci != null && ! empty ( $banci )) {
			// $updateFields ['班次'] = $banci [0] ['班次'];
			// }
			//Log::record ( $updCondition );
			//Log::record ( $updateFields );
			Db::connect ( $dbConfig )->execute ( " update tvhk set [姓名]='" . $orderInfo ['xingming'] . "' , [性别]='" . $orderInfo ['xingbie'] . "' , [证件] = '身份证' where [房号]='" . $rowValue . "'" );
			//Log::record ( " update tvhk set [证件编号]='" . $orderInfo ['shenfenzhenghao'] . "' , [入住天数]=" . $orderInfo ['yuzhutianshu'] . " , [宾客电话] = '" . $orderInfo ['dianhua'] . "' where [房号]='" . $rowValue . "'" );
			Db::connect ( $dbConfig )->execute ( " update tvhk set [证件编号]='" . $orderInfo ['shenfenzhenghao'] . "' , [入住天数]=" . $orderInfo ['yuzhutianshu'] . " , [宾客电话] = '" . $orderInfo ['dianhua'] . "' where [房号]='" . $rowValue . "'" );
			//Log::record ( " update tvhk set [宾客类别]='网订' , [入住天数]=" . $orderInfo ['yuzhutianshu'] . " where [房号]='" . $rowValue . "'" );
			Db::connect ( $dbConfig )->execute ( " update tvhk set [宾客类别]='网订' , [入住天数]=" . $orderInfo ['yuzhutianshu'] . " where [房号]='" . $rowValue . "'" );
			//Log::record ( " update tvhk set [网付流水]='" . $orderInfo ['dingdanhao'] . "' , [网订]='是',[房态]='F' , [预交押金] = " . $updateFields ['预交押金'] . "  where [房号]='" . $rowValue . "'" );
			Db::connect ( $dbConfig )->execute ( " update tvhk set [押金类型]='微信', [网付流水]='" . $transactionId . "' , [网订]='是',[房态]='F' ,[验证号] ='".$orderInfo ['validcode']."',[网约单号]='".$orderInfo ['dingdanhao']."',[预交押金] = " . $updateFields ['预交押金'] . "  where [房号]='" . $rowValue . "'" );
			//Log::record ( " update tvhk set [登记房价]='" . $updateFields ['登记房价'] . "' , [预交押金]=" . $updateFields ['预交押金'] . ",[房态]='F' , [验证号] = '".$orderInfo ['validcode']."',[网约单号]='".$orderInfo ['dingdanhao']."'  where [房号]='" . $rowValue . "'" );
			Db::connect ( $dbConfig )->execute ( " update tvhk set [登记房价]='" . $updateFields ['登记房价'] . "' , [预交押金]=" . $updateFields ['预交押金'] . ",[房态]='F' , [网付日期] = GETDATE()  where [房号]='" . $rowValue . "'" );
			
			// $condition ['dingdanhao'] = $orderId;
			// $setFileds=array();
			// $setFileds['status']=1;
			// $orderInfo = Db::name ( 'order' )->where ( $condition )->update($setFileds);
			Db::execute ( "update hotelorder set status=1 , paytime=GETDATE() where dingdanhao='" . $orderId . "'" );
			
			// 需要添加网约单号
			
			// Db::name ( 'tvhk' )->where ( 'id', $kefangInfo ['ID'])->update ( ['姓名' => 'thinkphp'] );
		}
		
		$fanghaos = $orderInfo ['yudinghao'];
		
		$fanghaos = explode ( ",", $fanghaos );
		foreach ( $fanghaos as $hao ) {
			// 写网约表
			$TWYB = array ();
			$TWYB ['店码'] = $orderInfo ['dianma'];
			$TWYB ['店名'] = $orderInfo ['dianming'];
			$TWYB ['房号'] = $hao;
			$TWYB ['姓名'] = $orderInfo ['xingming'];
			$TWYB ['手机号'] = $orderInfo ['dianhua'];
			$TWYB ['证类'] = '身份证';
			$TWYB ['证号'] = $orderInfo ['shenfenzhenghao'];
			$TWYB ['付款金额'] = $orderInfo ['yudingjine'];
			$TWYB ['付款类型'] = '微信';
			$TWYB ['网付流水'] = $transactionId;
			$TWYB ['网约单号'] = $orderInfo ['dingdanhao'];
			$TWYB ['付款日期'] = date ( 'Y-m-d H:i:s' );
			// $TWYB['付款日期']=date();
			$TWYB ['开房数'] = sizeof ( $fanghaos );
			//
					//
			$TWYB ['房型'] = $orderInfo ['dengji'];
			if($isVip){
				$TWYB ['房价']=$roomTypeInfo['网会价'];
			}else{
				$TWYB ['房价']=$roomTypeInfo['网订价'];
				//$TWYB ['房价'] = $orderInfo ['yudingjine'] / sizeof ( $fanghaos );
			}
			
			$TWYB ['预住天'] = $orderInfo ['yuzhutianshu'];
			$TWYB ['验证号'] = $orderInfo ['validcode'];
			Log::record ( $TWYB );
			$insertSql = "insert into twyb(店码,店名,房号,姓名,
					手机号,证类,证号,付款金额,付款类型,网付流水,网约单号,
					付款日期,开房数,房型,房价,
					预住天,验证号 ) values('" . $TWYB ['店码'] . "','" . $TWYB ['店名'] . "','" . $TWYB ['房号'] . "','" . $TWYB ['姓名'] . "','" . $TWYB ['手机号'] . "','" . $TWYB ['证类']."','".$TWYB ['证号']."',".$TWYB ['付款金额'].",'".$TWYB ['付款类型']."','".$TWYB ['网付流水']."','".$TWYB ['网约单号']."',GETDATE(),".$TWYB ['开房数'].",'".$TWYB ['房型']."',".$TWYB ['房价'].",".$TWYB ['预住天'].",'".$TWYB ['验证号']."')";
			Log::record ( $insertSql );
			Db::execute ($insertSql);
			Db::connect ( $dbConfig )->execute ($insertSql );
		}
		
		//
		$result ['success'] = true;
		$result ['mess'] = '支付验证成功。';
		return $result;
		
		// 修改房屋状态
	}
	/**
	 * 生成内部订单
	 */
	private function createInnerOrder($orderForm) {
		// 按个数找到几个房间然后把状态改为预定
		$orderNoandMondy = array ();
		$order = array ();
		// 往订单表插入一条记录同时把订单id返回给微信支付环节
		$order ['dingdanhao'] = $this->createOrderId ( $orderForm ['shenfenzhenghao'] );
		// time () . substr ( $orderForm ['shenfenzhenghao'], - 6, 6 );
		$order ['dianma'] = $orderForm ['dianma'];
		$order ['dianming'] = $orderForm ['dianming'];
		$order ['shenfenzhenghao'] = $orderForm ['shenfenzhenghao'];
		$order ['dengji'] = $orderForm ['dengji'];
		$order ['yudingjine'] = $orderForm ['deposit'] * $orderForm ['roomCount']*$orderForm ['tianshu'];
		$order ['yudingshijian'] = date ( 'Y-m-d H:i:s' );
		$order ['yudinghao'] = $orderForm ['rooms'];
		$order ['xingming'] = $orderForm ['xingming'];
		$order ['xingbie'] = $orderForm ['xingbie'];
		$order ['dianhua'] = $orderForm ['dianhua'];
		$order ['yuzhutianshu'] = $orderForm ['tianshu'];
		$order ['status'] = '0';
		$order ['validcode'] = $orderForm ['validCode'];
		Log::record ( $order );
		// $orderModel = new \app\index\model\HotelOrder ();
		// $orderModel->save ( $order );
		Db::name ( 'hotelorder' )->insert ( $order );
		$orderNoandMondy ['dingdanhao'] = $order ['dingdanhao'];
		$orderNoandMondy ['money'] = $order ['yudingjine'];
		return $orderNoandMondy;
	}
	private function createOrderId($shenfenzhenghao) {
		$currentTime = date ( "YmdHis" );
		// $currentTime=str_replace('-','',$currentTime);
		// $currentTime=str_replace(':','',$currentTime);
		// $currentTime=str_replace(' ','',$currentTime);
		return $currentTime . substr ( $shenfenzhenghao, - 6, 6 );
	}
	/**
	 * 计算出需要交的定金金额
	 *
	 * @param unknown $dengji        	
	 * @param unknown $roomCount        	
	 * @param unknown $tianshu        	
	 */
	public function calculateDeposit($dengji, $rooms, $tianshu, $shenfenzhenghao, $dianma) {
		$roomCount = $iparr = explode ( ",", $rooms );
		$roomCount = sizeof ( $roomCount );
		$dbType = config ( 'vip_db' )['type'];
		$dbConfig = $this->getkefangDBConfig ( $dianma );
		if ($dbType == 'mysql') {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $dengji . "' and ( `房态`= 'A' and `净`='') " );
		} else if ($dbType == 'sqlsrv') {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where [等级]='" . $dengji . "' and ( [房态]= 'A' and [净]='') " );
		} else {
			$kefngList = Db::connect ( $dbConfig )->query ( "select * from tvhk where `等级`='" . $dengji . "' and ( `房态`= 'A' and `净`='') " );
		}
		
		$result = array ();
		$result ['mess'] = $kefngList;
		$kefangCondition = array ();
		$kefangCondition ["等级"] = $dengji;
		$kefangInfo = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $kefangCondition )->find ();
		if (sizeof ( $kefngList ) < $roomCount) {
			$result ['success'] = false;
			$result ['mess'] = '房间数不够，总共有' . sizeof ( $kefngList ) . '间可预定房间';
		} else {
			$vipCondition = array ();
			$vipCondition ['证件号码'] = $shenfenzhenghao;
			$vipCondition ['证件类型'] = 1;
			//
			$vipDBConfig = $this->getVIPDBConfig ( $dianma );
			$vipInfo = Db::connect ( $vipDBConfig )->name ( 'tb_huiy' )->where ( $vipCondition )->select ();
			$isVip = false;
			if ($vipInfo != null && ! empty ( $vipInfo )) {
				$isVip = true;
			} else {
				$isVip = false;
			}
			$result ['success'] = true;
			if ($isVip) {
				$result ['deposit'] = $kefangInfo ['预定金额'] * $roomCount * $tianshu;
			} else {
				$result ['deposit'] = $kefangInfo ['预定金额'] * $roomCount * $tianshu;
			}
		}
		return $result;
	}
	public function queryOrders($phone, $validCode) {
		$condition = array ();
		$condition ['dianhua'] = $phone;
		$condition ['validcode'] = $validCode;
		//$condition ['shenfenzhenghao'] = $shenfenzhenghao;
		$orderInfos = Db::name ( 'hotelorder' )->where ( $condition )->order ( 'yudingshijian desc' )->select ();
		$reault=array();
		if(empty($orderInfos)||$orderInfos==null){
			$reault['success']=false;
			return $reault ;
		}
		$returnInfos=array();
		
		foreach ( $orderInfos as $orderInfo ) {
			$dbConfig = $this->getkefangDBConfig ( $orderInfo ['dianma'] );
			$dengji = $orderInfo ['dengji'];
			$kefangCondition ["等级"] = $dengji;
			$roomTypeInfo = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $kefangCondition )->find ();
			$orderInfo ['dengjiming'] = $roomTypeInfo ['名称'];
			array_push ( $returnInfos, $orderInfo );
		}
		$reault['success']=true;
		$reault['orderInfos']=$returnInfos;
		return $reault;
	}
	public function orderInfoById($orderId) {
		$condition = array ();
		$condition ['dingdanhao'] = $orderId;
		$orderInfo = Db::name ( 'hotelorder' )->where ( $condition )->find ();
		$dbConfig = $this->getkefangDBConfig ( $orderInfo ['dianma'] );
		$dengji = $orderInfo ['dengji'];
		$kefangCondition ["等级"] = $dengji;
		$roomTypeInfo = Db::connect ( $dbConfig )->name ( 'tb_djwj' )->where ( $kefangCondition )->find ();
		$orderInfo ['dengjiming'] = $roomTypeInfo ['名称'];
		return $orderInfo;
	}
	
	public function jdjs($dianma){
		$dbConfig = $this->getkefangDBConfig ($dianma);
		$jdjs = Db::connect ( $dbConfig )->name ( 'tb_sp' )->find ();
		Log::record ($jdjs);
		$condition = array ();
		$condition ['店码'] = $dianma;
		$shopInfo = Db::name ( 'tb_shop' )->where ( $condition )->find ();
		$result=array();
		$result['shopInfo']=$shopInfo;
		$result['jdjs']=$jdjs;
		return $result;
	}
}
