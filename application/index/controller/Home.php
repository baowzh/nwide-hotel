<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;
use Think\Log;

/**
 * 微信公众号介入
 *
 * @author bao_w
 *        
 */
class Home extends Controller {
	/**
	 * 公众号回调入口
	 *
	 * @param unknown $signature        	
	 * @param unknown $timestamp        	
	 * @param unknown $nonce        	
	 * @param unknown $echostr        	
	 */
	public function Index() {
		if ($this->request->isGet ()) {
			$signature = $this->request->param ( 'signature', "" );
			if ($signature == null || $signature == '') {
				echo "请填写'signature'";
				exit ();
			}
			$timestamp = $this->request->param ( 'timestamp', "" );
			if ($timestamp == null || $timestamp == '') {
				echo "请填写'timestamp'";
				exit ();
			}
			$nonce = $this->request->param ( 'nonce', "" );
			if ($nonce == null || $nonce == '') {
				echo "请填写'nonce'";
				exit ();
			}
			$echostr = $this->request->param ( 'echostr', "" );
			if ($echostr == null || $echostr == '') {
				echo "请填写'echostr'";
				exit ();
			}
			echo $echostr;
		} else {
			
			$file_in = file_get_contents ( "php://input" );
			Log::record ( $file_in );
			$nodes = $this->FromXml ( $file_in );
			$toUsername = $nodes ['FromUserName'];
			$fromUsername = $nodes ['ToUserName'];
			$textTpl = "<xml>
		  <ToUserName><![CDATA[%s]]></ToUserName>
		  <FromUserName><![CDATA[%s]]></FromUserName>
		  <CreateTime>%s</CreateTime>
		  <MsgType><![CDATA[%s]]></MsgType>
		  <Content><![CDATA[%s]]></Content>
		  <FuncFlag>0</FuncFlag>
		  </xml>";
			
			$contentStr = 'hello ';
			$date = date_create ();
			$time = date_timestamp_get ( $date );
			$resultStr = sprintf ( $textTpl, $toUsername, $fromUsername, $time, 'text', $contentStr );
			header ( 'Content-Type:application/xml; charset=utf-8' );
			Log::record ( $resultStr );
			echo $resultStr;
		}
	}
	private function FromXml($xml) {
		libxml_disable_entity_loader ( true );
		$this->values = json_decode ( json_encode ( simplexml_load_string ( $xml, 'SimpleXMLElement', LIBXML_NOCDATA ) ), true );
		return $this->values;
	}
}