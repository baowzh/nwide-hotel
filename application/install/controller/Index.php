<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace app\install\controller;
use think\Controller;
use think\Db;
use think\Storage;
class Index extends Controller{
	//安装首页
	public function index(){
		if(is_file(ROOT_PATH.'data/install.lock')){
			$this->error('已经成功安装了贝云cms，请不要重复安装!');
		}
		session('step', 0);
		session('error',null);
		return $this->fetch();
	}
	//安装完成
	public function complete(){
		$step = session('step');

		if(!$step){
			$this->redirect('index');
		} elseif($step != 3) {
		   $this->redirect("Install/step{$step}");
		}
		 $config= session('db_config');
		//动态定义连接信息
        unset($config['params']);
        $db=Db::connect($config);
		//注册管理员帐号
		$key=build_api_token();
		$admin = session('admin_info');
		insert_member($db,$config['prefix'], $admin, $key);
         
		//创建配置文件
		$conf=create_database($config, $key);
		session('config_file',$config);
		
		$this->assign('INSTALL_PATH',INSTALL_PATH);
        file_put_contents(ROOT_PATH  . 'data/install.lock',"yershop".date("Y-m-d h:i:s",time()));
		
		$this->assign('info',"管理员创建成功");
		session('step', null);
		session('error', null);
		return $this->fetch();
	}
}
