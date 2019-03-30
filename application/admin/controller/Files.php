<?php
// +----------------------------------------------------------------------
// | 贝云cms内容管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bycms.cn All rights reserved.
// +----------------------------------------------------------------------
// | 版权申明：贝云cms内容管理系统不是一个自由软件，是贝云网络官方推出的商业源码，严禁在未经许可的情况下
// | 拷贝、复制、传播、使用贝云cms内容管理系统的任意代码，如有违反，请立即删除，否则您将面临承担相应
// | 法律责任的风险。如果需要取得官方授权，请联系官方http://www.bycms.cn
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Db;
use think\File;
use think\Validate;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class Files extends Admin {
	  /* 文件上传 */
    public function add($name){
		$name=input("name");
		$file = request()->file($name);
        if(!$name){
            $this->error('参数错误！');
        }
      // 移动到框架应用根目录/public/uploads/ 目录下
      
        $info =$file->validate(['size'=>300*1024*1024,'ext'=>'zip,mp4,MP4,mp3,flv,jpg,png,gif,doc,pdf,excel,ppt'])->move(ROOT_PATH  . DS . 'uploads/Attachment');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
              $path="uploads/Attachment/".$info->getSaveName();
              $data["path"]='/'.str_replace("\\","/",$path);
			  $data["oldname"]=$_FILES[$name]["name"];
			  $data["exts"]=$info->getExtension();
		      $data["status"]=1;
			  $data["size"]=$_FILES[$name]["size"];
		      $data["create_time"]=time();
			  $id=db('attachment')->insertGetId($data);
				// 返回
			header('content-type:application/json;charset=utf-8');
			$INSTALL_PATH = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']));
			if($INSTALL_PATH==="/"){
				  $INSTALL_PATH="";		
			}else{
				$INSTALL_PATH= '/'. trim($INSTALL_PATH,'/').'';
			}
			$data = array('path'=>$INSTALL_PATH.$data["path"],'msg'=>'上传成功','id'=>$id,"code"=>1);
			echo json_encode($data);

        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
   
	
    }

    /* 文件上传 */
    public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = model('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);

        /* 记录附件信息 */
        if($info){
            $return['data'] = think_encrypt(json_encode($info['download']));
            $return['info'] = $info['download']['name'];
        } else {
            $return['status'] = 0;
            $return['info']   = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 下载文件 */
    public function download($id = null){
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }

        $logic = D('Download', 'Logic');
        if(!$logic->download($id)){
            $this->error($logic->getError());
        }

    }
public function upload2(){
    // 获取表单上传文件 例如上传了001.jpg
    $file = request()->file('image');
    // 移动到框架应用根目录/public/uploads/ 目录下
    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
    if($info){
        // 成功上传后 获取上传信息
        // 输出 jpg
        echo $info->getExtension();
        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
        echo $info->getSaveName();
        // 输出 42a79759f284b767dfcb2a0197904287.jpg
        echo $info->getFilename(); 
    }else{
        // 上传失败获取错误信息
        echo $file->getError();
    }
}
   public function uploadpicture(){
       
        /* 调用文件上传组件上传文件 */
		 $Picture= new \app\admin\model\Picture;
         $driver = config('PICTURE_UPLOAD_DRIVER');
         $info = $Picture->upload(
            $_FILES,
            config('PICTURE_UPLOAD'),
            config('PICTURE_UPLOAD_DRIVER'),
            config("UPLOAD_{$driver}_CONFIG")
        ); //TODO:上传到远程服务器
      addUserLog(var_export($info,true),1);
        /* 记录图片信息 */
        if($info){
            $return["path"]=site_url().$info["path"];
			$return['status'] = 1;
			$return['id'] = $info["id"];
           
        } else {
            $return['status'] = 0;
			$error=$Picture->getError()?$Picture->getError():"上传失败！";
            $return['info']   = $error;
        }
        /* 返回JSON数据 */
        exit(json_encode($return));
    }
}
