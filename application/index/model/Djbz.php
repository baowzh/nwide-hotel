<?php

namespace app\index\model;

use think\Model;
use think\Db;

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
	/**
	 * 查询客房信息并把汉字列转换为拼音列返回
	 *
	 * @var arry[array]
	 */
	public function query() {
		$list = Db::name ( 'tb_djbz' )->select ();
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
	public function order($dengji, $xingming, $xingbie, $shenfenzhenghao, $dianhua, $zhifufangshi) {
		// 1.检查是否能够预定
		$kefangCondition = array ();
		$kefangCondition ["等级"] = $dengji;
		$kefangInfo = Db::name ( 'tb_djbz' )->where ( $kefangCondition )->find();
		// 是否可以预定？
	    
		
		// 2.查询是否为会员
		$vipCondition = [ 
				'证件号码' => '',
				'证件类型' => '' 
		];
		$vipCondition ['证件号码'] = $shenfenzhenghao;
		$vipCondition ['证件类型'] = 1;
		$vipInfo = Db::connect ( config ( 'vip_db' ) )->name ( 'tb_huiy' )->where ( $vipCondition )->select ();
		$isVip = false;
		if ($vipInfo != null && ! empty ( $vipInfo )) {
			$isVip = true;
		} else {
			$isVip = false;
		}
		// 如果是 会员用什么价格去计算？
		if($isVip){

			
			
		}
		
		$shopCondition = [ 
				'店码' => config ( 'shop_id' ) 
		];
		// 3.查询微信支付相关参数
		$shopInfo = Db::connect ( config ( 'config_db' ) )->name ( 'tb_shop' )->where ( $shopCondition )->find ();
		if ($shopInfo != null && ! empty ( $shopInfo )) {
			//return $shopInfo;
		} else {
// 			return [ 
// 					'success' => 'true' 
// 			];
		}
		
		// 4.生成订单
		/*
		$qrcode_path=config('upload_path').'/'.'qrcode/'.'aa/';
		$filepath=ROOT_PATH.$qrcode_path;
		$fileName=$filepath.'aaa.png';
		qrcode('www.baidu.com',$fileName);
		*/
		
		$order=array(
				'body' => '测试描述',// 商品描述（需要根据自己的业务修改）
				'total_fee' => 1,// 订单金额  以(分)为单位（需要根据自己的业务修改）
				'out_trade_no' => time().rand(1000,9999),// 订单号（需要根据自己的业务修改）
				'product_id' => '234242342',// 商品id（需要根据自己的业务修改）
				'trade_type' => 'MWEB',// JSAPI公众号支付
		);
		//统一下单 获取prepay_id
		$redirect_url=urlencode('http://'.$_SERVER['HTTP_HOST'].'/index.php');  //支付完成后跳回地址
		$weixin = new \app\index\model\WeixinH4Pay();
		$unified_order= $weixin->unifiedOrder($order);
		//$this->redirect($unified_order['mweb_url']."&redirect_url=".$redirect_url);
		return $unified_order;
		
	}
	/**
	 * 微信支付回调接口，在这里面做订房操作
	 * @param unknown $orderId
	 */
	public function notify($orderId){
		
		// 查询班次
		
		// 生成客房订单
		
		// 修改房屋状态
		
	}
	/**
	 * 生成内部订单
	 */
	private function createInnerOrder(){
		
		
		
	}
}
