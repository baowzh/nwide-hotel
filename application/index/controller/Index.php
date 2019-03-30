<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model;
use think\Loader;

class Index extends Controller {
	/**
	 * 程序入口
	 * 
	 * @return \think\mixed
	 */
	public function index() {
		$djbz = new \app\index\model\Djbz ();
		$list = $djbz->query ();
		$this->assign ( 'kefangs', $list );
		return $this->fetch ();
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
		$zhifufangshi = $this->request->param ( 'zhifufangshi' );
		$djbz = new \app\index\model\Djbz ();
		$oderIfo = $djbz->order ( $dengji, $xingming, $xingbie, $shenfenzhenghao, $dianhua, $zhifufangshi );
		$redirect_url = urlencode ( 'http://' . $_SERVER ['HTTP_HOST'] . '/index.php' );
		$this->redirect ( $oderIfo ['mweb_url'] . "&redirect_url=" . $redirect_url );
	}
	/**
	 * 支付回调入口
	 */
	public function notify() {
		$weixin = new \app\index\model\WeixinH4Pay();
		$result=$weixin->notify();
		if ($result) {
			//完成支付后处理业务逻辑
			
			
		
		}
	}
	
	public function qrcode(){
		
		Loader::import ( 'phpqrcode/phpqrcode', EXTEND_PATH );
		$qr = new \QRcode();
		$qr->png('www.baidu.com');
	}
	
	
	
	
}
