<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model;
use think\Loader;

Loader::import ( 'wxpay.lib.WxPay', EXTEND_PATH, '.Api.php' );
Loader::import ( 'wxpay.example.WxPay', EXTEND_PATH, '.JsApiPay.php' );
Loader::import ( 'wxpay.example.log', EXTEND_PATH, '.php' );
Loader::import ( 'wxpay.lib.WxPay', EXTEND_PATH, '.Config.php' );
class Index extends Controller {
	
	public function index() {
		//echo dirname(__FILE__);
		//exit(0);
		$djbz = new \app\index\model\Djbz ();
		$hotels = $djbz->index ();
		$this->assign ( 'hotels', $hotels );
		return $this->fetch ();
	}
	
	/**
	 * 酒店客房列表
	 *
	 * @return \think\mixed
	 */
	public function catalog() {
		$djbz = new \app\index\model\Djbz ();
		$list = $djbz->query ();
		$this->assign ( 'kefangs', $list );
		
		return $this->fetch ();
	}
	/**
	 * 跳转到订单界面
	 * @param unknown $dengji
	 * @return \think\mixed
	 */
	public function toOrder($dengji) {
		$djbz = new \app\index\model\Djbz ();
		$djbzVo = $djbz->findDJbz ( $dengji );
		$this->assign ( 'vo', $djbzVo );
		return $this->fetch ( 'order' );
	}
	/**
	 * 把客房相关的视频文件读出来并写入本地磁盘，同时返回相对路径用于播放视频
	 */
	public function video($id) {
		$djbz = new \app\index\model\Djbz ();
		$videoPath = $djbz->getVideo ( $id );
		header ( 'Content-Type:application/json; charset=utf-8' );
		return json_encode ( [ 
				"success" => true,
				'videoPath' => $videoPath 
		] );
	}
	
	/**
	 * 把客房相关的图片写入本地磁盘并返回相对路径用于显示图片
	 */
	public function image($id) {
		$djbz = new \app\index\model\Djbz ();
		$imagePath = $djbz->getImage ( $id );
		header ( 'Content-Type:application/json; charset=utf-8' );
		return json_encode ( [ 
				"success" => true,
				'imagePath' => $imagePath 
		] );
	}
	/**
	 * 订购房间
	 *
	 * @param unknown $id        	
	 */
	public function order() {
		$dengji = $this->request->param ( 'dengji' );
		$xingming = $this->request->param ( 'xingming' );
		$xingbie = $this->request->param ( 'xingbie' );
		$shenfenzhenghao = $this->request->param ( 'shenfenzhenghao' );
		$dianhua = $this->request->param ( 'dianhua' );
		$zhifufangshi = $this->request->param ( 'zhifufangshi', 1 );
		$roomCount = $this->request->param ( 'roomCount', 1 );
		$tianshu = $this->request->param ( 'tianshu', 1 );
		$djbz = new \app\index\model\Djbz ();
		$orderForm = array ();
		$orderForm ['dengji'] = $dengji;
		$orderForm ['xingming'] = $xingming;
		$orderForm ['xingbie'] = $xingbie;
		$orderForm ['shenfenzhenghao'] = $shenfenzhenghao;
		$orderForm ['dianhua'] = $dianhua;
		$orderForm ['roomCount'] = $roomCount;
		$orderForm ['tianshu'] = $tianshu;
		
		$oderIfo = $djbz->order ( $orderForm );
		return json ( $oderIfo );
		/*
		 * $redirect_url = urlencode ( 'http://' . $_SERVER ['HTTP_HOST'] . '/index.php' );
		 * $this->redirect ( $oderIfo ['mweb_url'] . "&redirect_url=" . $redirect_url );
		 */
	}
	/**
	 * 支付回调入口
	 */
	public function notify() {
		$weixin = new \app\index\model\WeixinH4Pay ();
		$result = $weixin->notify ();
		if ($result) {
			// 完成支付后处理业务逻辑
		}
	}
	/**
	 * 生成支付二维码
	 */
	public function qrcode() {
		Loader::import ( 'phpqrcode/phpqrcode', EXTEND_PATH );
		$qr = new \QRcode ();
		$qr->png ( 'www.baidu.com' );
	}
	/**
	 * 计算定金
	 * @param unknown $dengji
	 * @param unknown $roomCount
	 * @param unknown $tianshu
	 * @param unknown $shenfenzhenghao
	 * @return \think\response\Json
	 */
	public function calculateDeposit($dengji, $roomCount, $tianshu, $shenfenzhenghao) {
		$djbz = new \app\index\model\Djbz ();
		$deposit = $djbz->calculateDeposit ( $dengji, $roomCount, $tianshu, $shenfenzhenghao );
		header ( 'Content-Type:application/json; charset=utf-8' );
		return json ( [ 
				"success" => true,
				'Deposit' => $deposit 
		] );
	}
	/**
	 * jspai 支付
	 * 
	 * @return \think\response\Json|\think\mixed
	 */
	public function jsapiPay() {
		$tools = new \JsApiPay ();
		if ($this->request->isPost ()) {
			$data = input ( 'post.' );
			$money = $data ['money'] * 100;
			$openId = session ( 'openid' );
			$userId = session ( 'id' );
			$input = new \WxPayUnifiedOrder ();
			$input->SetBody ( "test" ); // 商品描述
			$input->SetAttach ( $userId ); // 附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用
			$input->SetOut_trade_no ( \WxPayConfig::MCHID . date ( "YmdHis" ) ); // 商户订单号
			$input->SetTotal_fee ( $money ); // 订单金额
			$input->SetTime_start ( date ( "YmdHis" ) ); // 交易起始时间
			$input->SetTime_expire ( date ( "YmdHis", time () + 600 ) ); // 交易结束时间
			$input->SetGoods_tag ( "test" ); // 订单优惠标记，使用代金券或立减优惠功能时需要的参数，实际上这里可以不要
			$input->SetNotify_url ( "http://www.xxxx.com/wechat/index.php/server/pay/notify" ); // 接收回调通知地址
			$input->SetTrade_type ( "JSAPI" ); // 支付类型
			$input->SetOpenid ( $openId ); // 用户openid
			$order = \WxPayApi::unifiedOrder ( $input ); // 统一下单，该方法中包含了签名算法
			$jsApiParameters = $tools->GetJsApiParameters ( $order ); // 统一下单参数
			                                                          // 将统一下单接口生成的预支付订单参数返回给前端，前端就可以调取支付了
			                                                          // return getBack ( 1, $jsApiParameters ); // getBack是我自定义的方法，就是给前端ajax请求返回json格式数据，1代表成功，这里你要自己修改。
			return json ( $jsApiParameters );
		} else {
			// 下面是展示前端页面的，与统一下单无关
			$openId = session ( 'openid' );
			$this->assign ( 'user', session ( 'username' ) );
			$this->assign ( 'openId', $openId );
			return $this->fetch ( 'recharge' );
		}
	}
}
