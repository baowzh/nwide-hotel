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
			'可售' => 'keshou' 
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
			'keshou' => '可售' 
	];
	
	
	public function index(){
		$shopInfos = Db::connect ( config ( 'config_db' ) )->name ( 'tb_shop' )->select();
		return $shopInfos;
		
	}
	
	
	public function listByCatelog($dianma,$dengji){
		$dbConfig=$this->getkefangDBConfig($dianma);
		//Log::record ($dbConfig );
		$result=array();
		// 是否可以预定？
		$dbType = config ( 'config_db' )['type'];
		if ($dbType == 'mysql') {
			$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $dengji . "' and ( `房态`= 'A' or `净`='') " );
		} else if ($dbType == 'sqlsrv') {
			$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where [等级]='" . $dengji . "' and ( [房态]= 'A' or [净]='') " );
		} else {
			$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $dengji . "' and ( `房态`= 'A' or `净`='') " );
		}
		$result['kefngList']=$kefngList;
		$result['vo']=$this->findDJbz($dengji, $dianma)['info'];
       return 	$result;
	}
	/**
	 * 查询客房信息并把汉字列转换为拼音列返回
	 *
	 * @var arry[array]
	 */
	public function query($dianma) {
		//
		$condition=array();
		$condition['店码']=$dianma;
		$shopInfo = Db::connect ( config ( 'config_db' ) )->name ( 'tb_shop' )->where($condition)->find();
		$dbConfig=$this->getkefangDBConfig($dianma);
		Log::record ($dbConfig );
		$list = Db::connect($dbConfig)->name ( 'tb_djbz' )->select ();
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
		
		return $convertedValues;
	}
	private function getkefangDBConfig($dianma){
		
		$condition=array();
		$condition['店码']=$dianma;
		$shopInfo = Db::connect ( config ( 'config_db' ) )->name ( 'tb_shop' )->where($condition)->find();
		$dbConfig=array();
		$dbType = config ( 'config_db' )['type'];
		$dbConfig['type']=$dbType;
		$dbConfig['hostname']=trim($shopInfo['服务器'],"\r");
		$dbConfig['database']=trim($shopInfo['数据库'],"\r");
		$dbConfig['username']=trim($shopInfo['用户'],"\r");
		$dbConfig['password']=trim($shopInfo['密码'],"\r");
		//$dbConfig['hostport']=config ( 'config_db' )['hostport'];
		return $dbConfig;
		
	}
	/**
	 * 生成查看的视频文件并返回url
	 *
	 * @param unknown $dengji        	
	 * @return string
	 */
	public function getVideo($dengji) {
		$where = array ();
		$where ["等级"] = $dengji;
		
		$info = Db::name ( 'tb_djbz' )->where ( $where )->find ();
		// 已经存在视频的话不写文件
		$extName = $info ['视频格式'] == null ? 'mp4' : $info ['视频格式'];
		$filePath = ROOT_PATH . '/uploads/' . $info ['等级'] . '.' . $extName;
		if (! file_exists ( $filePath )) {
			$file = fopen ( ROOT_PATH . '/uploads/' . $info ['等级'] . '.' . $extName, 'w' );
			$txt = $info ['视频'];
			// 16进制转为2进制
			$txt=hex2bin($txt);
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
	public function getImage($dengji) {
		$where = array ();
		$where ["等级"] = $dengji;
		$info = Db::name ( 'tb_djbz' )->where ( $where )->find ();
		$extName = $info ['图片格式'] == null ? '.jpeg' : $info ['图片格式'];
		// 已经存在视频的话不写文件
		$filePath = ROOT_PATH . '/uploads/' . $info ['等级'] . '.' . $extName;
		if (! file_exists ( $filePath )) {
			$file = fopen ( ROOT_PATH . '/uploads/' . $info ['等级'] . '.' . $extName, 'w' );
			$txt = $info ['图片'];
			fwrite ( $file, $txt );
		}
		return '/uploads/' . $info ['等级'] . '.' . $extName;
	}
	/**
	 * 订购房间并生成微信支付订单信息
	 */
	public function order($orderForm) {
		$result = array ();
		$kefangCondition = array ();
		$dbConfig=$this->getkefangDBConfig($orderForm['dianma']);
		$kefangCondition ["等级"] = $orderForm ['dengji'];
		$kefangInfo = Db::connect($dbConfig)->name ( 'tb_djbz' )->where ( $kefangCondition )->find ();
		// 是否可以预定？
		$dbType = config ( 'vip_db' )['type'];
		if ($dbType == 'mysql') {
			$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' or `净`='') " );
		} else if ($dbType == 'sqlsrv') {
			$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where [等级]='" . $orderForm ['dengji'] . "' and ( [房态]= 'A' or [净]='') " );
		} else {
			$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' or `净`='') " );
		}
		$rooms= explode (",", $orderForm ['rooms'] );
		$roomCount=sizeof($rooms);
		if (sizeof ( $kefngList ) < $roomCount) {
			$result ['success'] = false;
			$result ['mess'] = '房间数不够';
			return $result;
		}
		// 校验每个房间的状态
		foreach ( $rooms as $rowValue ) {
			$dbType = config ( 'vip_db' )['type'];
			if ($dbType == 'mysql') {
				$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' or `净`='')  and '房号'='".$rowValue."'" );
			} else if ($dbType == 'sqlsrv') {
				$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where [等级]='" . $orderForm ['dengji'] . "' and ( [房态]= 'A' or [净]='') and '[房号]'='".$rowValue."'"  );
			} else {
				$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' or `净`='') and '房号'='".$rowValue."'" );
			}
			if($kefngList==null||sizeof($kefngList)==0){
				$result ['success'] = false;
				$result ['mess'] = '房间'.$rowValue.'已被预订，请选择其他房间';
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
		$vipInfo = Db::connect ( config ( 'vip_db' ) )->name ( 'tb_huiy' )->where ( $vipCondition )->select ();
		$isVip = false;
		if ($vipInfo != null && ! empty ( $vipInfo )) {
			$isVip = true;
		} else {
			$isVip = false;
		}
		$orderForm ['deposit'] = $kefangInfo ['网订价'];
		// 如果是 会员用什么价格去计算？
		if ($isVip) {
			$orderForm ['deposit'] = $kefangInfo ['网会价'];
		}
		
		$shopCondition = [ 
				'店码' => config ( 'shop_id' ) 
		];
		// 3.查询微信支付相关参数
		$shopInfo = Db::connect ( config ( 'config_db' ) )->name ( 'tb_shop' )->where ( $shopCondition )->find ();
		if ($shopInfo != null && ! empty ( $shopInfo )) {
			$orderForm ['dianma'] = $shopInfo ['店码'];
			$orderForm ['dianming'] = $shopInfo ['店名'];
		} else {
			return [ 
					'success' => 'true',
					'mess' => '未找到支付相关参数设置' 
			];
		}
		
		// 4.生成订单
		$orderForm ['kefngList'] = $kefngList;
		$orderInfo = $this->createInnerOrder ( $orderForm );
		
		$order = array (
				'body' => '宾馆客房预订资金', // 商品描述（需要根据自己的业务修改）
				'total_fee' => $orderInfo['money'], // 订单金额 以(分)为单位（需要根据自己的业务修改）
				'out_trade_no' =>$orderInfo['dingdanhao'] , // 订单号（需要根据自己的业务修改）
				'product_id' => $orderInfo['dingdanhao'] // 商品id（需要根据自己的业务修改）
				//'trade_type' => 'MWEB' 
		); 
		   
		return $order;
	}
	public function findDJbz($dengji,$dianma) {
		$where = array ();
		$where ["等级"] = $dengji;
		$dbConfig=$this->getkefangDBConfig($dianma);
		$info = Db::connect($dbConfig)->name ( 'tb_djbz' )->where ( $where )->find ();
		$condition=array();
		$condition['店码']=$dianma;
		$shopInfo = Db::connect ( config ( 'config_db' ) )->name ( 'tb_shop' )->where($condition)->find();
		$result=array();
		$result['info']=$info;
		$result['shopInfo']=$shopInfo;
		return $result;
	}
	/**
	 * 微信支付回调接口，在这里面做订房操作
	 *
	 * @param unknown $orderId        	
	 */
	public function notify($orderId) {
		// 查看订单表 如果有未支付的订单则
		$result = array ();
		$condition = array ();
		$condition ['dingdanhao'] = $orderId;
		$orderInfo = Db::name ( 'order' )->where ( $condition )->find ();
		if ($orderInfo == null || empty ( $orderInfo )) {
			$result ['success'] = false;
			return $result;
		}
		
		// 查询班次
		
		$banci = Db::query ( "select max('班次') from tbanci" );
		
		$dbType = config ( 'vip_db' )['type'];
		if ($dbType == 'mysql') {
			$kefngList = Db::query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' or `净`='') " );
		} else if ($dbType == 'sqlsrv') {
			$kefngList = Db::query ( "select * from tvhk where [等级]='" . $orderInfo ['dengji'] . "' and ( [房态]= 'A' or [净]='') " );
		} else {
			$kefngList = Db::query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' or `净`='') " );
		}
		// 生成客房订单
		$yudinghao=$orderInfo['yudinghao'];
		$yudinghaos=explode (",", $yudinghao);
		$yudingshu = sizeof($yudinghaos);
		if (sizeof ( $kefngList ) < $yudingshu) {
			$result ['success'] = false;
			$result ['mess'] = '房间数不够';
			return $result;
		}
		
		
		//
		
		
		foreach ( $yudinghaos as $rowValue ) {
			$dbType = config ( 'vip_db' )['type'];
			if ($dbType == 'mysql') {
				$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' or `净`='')  and '房号'='".$rowValue."'" );
			} else if ($dbType == 'sqlsrv') {
				$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where [等级]='" . $orderForm ['dengji'] . "' and ( [房态]= 'A' or [净]='') and '[房号]'='".$rowValue."'"  );
			} else {
				$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $orderInfo ['dengji'] . "' and ( `房态`= 'A' or `净`='') and '房号'='".$rowValue."'" );
			}
			if($kefngList==null||sizeof($kefngList)==0){
				$result ['success'] = false;
				$result ['mess'] = '房间'.$rowValue.'已被预订，请选择其他房间';
				return $result;
			}
		
		}
		//
		
		
		
		
		$vipCondition = array ();
		$vipCondition ['证件号码'] = $orderInfo ['shenfenzhenghao'];
		$vipCondition ['证件类型'] = 1;
		$vipInfo = Db::connect ( config ( 'vip_db' ) )->name ( 'tb_huiy' )->where ( $vipCondition )->select ();
		$isVip = false;
		if ($vipInfo != null && ! empty ( $vipInfo )) {
			$isVip = true;
		} else {
			$isVip = false;
		}
		//
		
		$kefangCondition = array ();
		$kefangCondition ["等级"] = $orderInfo ['dengji'];
		$roomTypeInfo = Db::name ( 'tb_djbz' )->where ( $kefangCondition )->find ();
		
		foreach( $yudinghaos as $rowValue ) {
			//$kefangInfo = $kefngList [$index];
			//print_r($orderInfo);
			// 修改为预定
			$updateFields = array ();
			$updCondition = array ();
			$updCondition ['房号'] = $rowValue;
			$updateFields ['姓名'] = $orderInfo ['xingming'];
			/*
			$updateFields ['性别'] = $orderInfo ['xingbie'];
			$updateFields ['证件'] = '身份证';
			$updateFields ['证件编号'] = $orderInfo ['shenfenzhenghao'];
			$updateFields ['入住天数'] = $orderInfo ['tianshu'];
			$updateFields ['入住日期'] = date ( 'Y-m-d H:i:s' );
			$updateFields ['宾客电话'] = $orderInfo ['dianhua'];
			$updateFields ['宾客类别'] = '网订';
			$updateFields ['网订'] = '是';
			// $updateFields['网订']='是';
			$updateFields ['房态'] = 'E';*/
			
			if ($isVip) {
				$updateFields ['预交押金'] = $roomTypeInfo ['网会价'];
			} else {
				$updateFields ['预交押金'] = $roomTypeInfo ['网订价'];
			}
			//$updateFields ['网付流水'] = $orderInfo ['dingdanhao'];
// 			if ($banci != null && ! empty ( $banci )) {
// 				$updateFields ['班次'] = $banci [0] ['班次'];
// 			}
			Log::record ($updCondition );
			Log::record ($updateFields );
			Db::execute("update tvhk set [姓名]='".$orderInfo ['xingming']."' , [性别]='".$orderInfo ['xingbie']."' , [证件] = '身份证' where [房号]='".$rowValue."'");
			Db::execute("update tvhk set [证件编号]='".$orderInfo ['shenfenzhenghao']."' , [入住天数]=".$orderInfo ['tianshu']." , [宾客电话] = '".$orderInfo ['dianhua']."' where [房号]='".$rowValue."'");
			Db::execute("update tvhk set [宾客类别]='网订' , [入住天数]=".$orderInfo ['tianshu']. " where [房号]='".$rowValue."'");
			Db::execute("update tvhk set [网付流水]='".$orderInfo ['dingdanhao']."' , [网订]='是',[房态]='E' , [预交押金] = ".$updateFields ['预交押金']."  where I[房号]=".$rowValue."'");
			Db::execute("update tvhk set [登记房价]='".$updateFields ['预交押金']."' , [预交押金]=".$updateFields ['预交押金'].",[房态]='E' , [网付日期] = GETDATE()
					  where [房号]='".$rowValue."'");
			
			// 需要添加网约单号 
			
			
			
			
			//Db::name ( 'tvhk' )->where ( 'id', $kefangInfo ['ID'])->update ( ['姓名' => 'thinkphp'] );
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
		$orderNoandMondy=array();
		$order = array ();
		// 往订单表插入一条记录同时把订单id返回给微信支付环节
		$order ['dingdanhao'] = time () . substr ( $orderForm ['shenfenzhenghao'], - 6, 6 );
		$order ['dianma'] = $orderForm ['dianma'];
		$order ['dianming'] = $orderForm ['dianming'];
		$order ['shenfenzhenghao'] = $orderForm ['shenfenzhenghao'];
		$order ['dengji'] = $orderForm ['dengji'];
		$order ['yudingjine'] = $orderForm ['deposit'] * $orderForm ['roomCount'];
		$order ['yudingshijian'] = date ( 'Y-m-d H:i:s' );
		$order ['yudinghao'] = $orderForm ['rooms'];
		$order ['xingming'] = $orderForm ['xingming'];
		$order ['xingbie'] = $orderForm ['xingbie'];
		$order ['dianhua'] = $orderForm ['dianhua'];
		$order ['yuzhutianshu'] = $orderForm ['tianshu'];
		$order ['status'] = '0';
		$orderModel = new \app\index\model\Order ();
		$orderModel->save ( $order );
		$orderNoandMondy['dingdanhao']=$order ['dingdanhao'] ;
		$orderNoandMondy['money']=$order ['yudingjine'] ;
		return $orderNoandMondy;
	}
	/**
	 * 计算出需要交的定金金额
	 *
	 * @param unknown $dengji        	
	 * @param unknown $roomCount        	
	 * @param unknown $tianshu        	
	 */
	public function calculateDeposit($dengji, $rooms, $tianshu, $shenfenzhenghao,$dianma) {
		$roomCount=$iparr = explode (",", $rooms); 
		$roomCount=sizeof($roomCount);
		$dbType = config ( 'vip_db' )['type'];
		$dbConfig=$this->getkefangDBConfig($dianma);
		if ($dbType == 'mysql') {
			$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $dengji . "' and ( `房态`= 'A' or `净`='') " );
		} else if ($dbType == 'sqlsrv') {
			$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where [等级]='" . $dengji . "' and ( [房态]= 'A' or [净]='') " );
		} else {
			$kefngList = Db::connect($dbConfig)->query ( "select * from tvhk where `等级`='" . $dengji . "' and ( `房态`= 'A' or `净`='') " );
		}
		
		$result = array ();
		$result ['mess'] = $kefngList;
		$kefangCondition = array ();
		$kefangCondition ["等级"] = $dengji;
		$kefangInfo = Db::connect($dbConfig)->name ( 'tb_djbz' )->where ( $kefangCondition )->find ();
		if (sizeof ( $kefngList ) < $roomCount) {
			$result ['success'] = false;
			$result ['mess'] = '房间数不够，总共有' . sizeof ( $kefngList ) . '间可预定房间';
		} else {
			$vipCondition = array ();
			$vipCondition ['证件号码'] = $shenfenzhenghao;
			$vipCondition ['证件类型'] = 1;
			$vipInfo = Db::connect ( config ( 'vip_db' ) )->name ( 'tb_huiy' )->where ( $vipCondition )->select ();
			$isVip = false;
			if ($vipInfo != null && ! empty ( $vipInfo )) {
				$isVip = true;
			} else {
				$isVip = false;
			}
			$result ['success'] = true;
			if ($isVip) {
				$result ['deposit'] = $kefangInfo ['网会价'] * $roomCount * $tianshu;
			} else {
				$result ['deposit'] = $kefangInfo ['网订价'] * $roomCount * $tianshu;
			}
		}
		return $result;
		
		
	}
	
	public function queryOrders($phone,$validCode){
		$condition = array ();
		$condition ['dinahua'] = $phone;
		$condition ['validcode'] = $validCode;
		$orderInfo = Db::name ( 'order' )->where ( $condition )->find ();
		return $orderInfo;
	}
	
	
	
	
	
}
