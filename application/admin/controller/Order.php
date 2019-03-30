<?php
// +----------------------------------------------------------------------
// | yershop网店管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.yershop.com All rights reserved.
// +----------------------------------------------------------------------
// | 版权申明：yershop网店管理系统不是一个自由软件，是贝云网络官方推出的商业源码，严禁在未经许可的情况下
// | 拷贝、复制、传播、使用yershop网店管理系统的任意代码，如有违反，请立即删除，否则您将面临承担相应
// | 法律责任的风险。如果需要取得官方授权，请联系官方http://www.yershop.com
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Order extends Admin{
    /**
     * 订单管理首页
     */
    public function index(){
		
		 if($_POST){
			foreach ($_POST as $key=>$value){
				if($value!=""){
					$map[$key]  = array('like', '%'.$value.'%');
				 }
			}
        }
		if (isset($map['start_time']) ) {
			  unset($map['start_time']);
              $map['create_time'] = array('egt',strtotime($_POST['start_time']));
        }
        if (isset($map['end_time']) ) {
			unset($map['end_time']);
            $map['create_time'] = array('elt',strtotime($_POST['end_time']));
        }
		$map=isset($map)?$map:'';
        $res= getLists('Order',$map,10,'id desc');
        $this->assign( 'res', $res);
        $this->meta_title = '订单管理';
		$this->assign('meta_title', $this->meta_title);
        return $this->fetch(); 
    }
      /**
     * 订单编辑
     */
	public function edit($id){   
	    if($_POST){
		   $Order =new \app\admin\model\Order;
           $res=$Order->validate(true)->allowField(true)->save($_POST,['id' => $_POST['id']]);
	       if($res){
		      $this->success("更新成功！",cookie("__forward__"));
		   }else{
			  $error=$Order->getError();
			  $this->error($error?$error:"更新失败");
		   } 
	  }
	  else{
		 $map['id']=$id;
		 $info=db("Order")->where($map)->find();
	     $this->assign('info', $info);
		 cookie("__forward__",input('server.HTTP_REFERER'));
		 $this->meta_title="编辑订单";
		 $this->assign('meta_title', $this->meta_title);
	     return $this->fetch();
	   }
	} 
	
	/**
     * 新增订单
     */
	  public function add($id=""){  
		  if($_POST){
			   $Order =new \app\admin\model\Order;
			// 过滤post数组中的非数据表字段数据
			   $Order=$Order->validate(true)->allowField(true)->save($_POST);
		   if($res){
			   $this->success("新增成功！",cookie("__forward__"));
			}else{
				$error=$Order->getError();
				$this->error($error?$error:"新增失败");
			} 
		}
		  else{
			  cookie("__forward__",input('server.HTTP_REFERER'));
			  $this->meta_title="新增订单";
			  $this->assign('meta_title', $this->meta_title);
			  return $this->fetch("order/edit");
		  }
	}
	public function out(){    
        $xlsName  = "Order";      
      
        
		$xlsCell  = array(
            array('id','编号'),
			array('order_sn','订单编号'),
            array('total_money','价格'),
            array('saleslist','订单内容'),
            array('username','联系人'),
            array('mobile','联系方式'), 
			array('total','供应商'),
            
			array('address','地址'), 
			array('status','订单状态'),
			array('express','快递名称'), 
			array('express_code','快递单号'),
			array('isdao','是否到货'), 
			array('arrive_time','到货时间'), 
			
			array('uid','用户uid')
        );
        $xlsModel = db($xlsName);
        $xlsData  = $xlsModel->select();      
		 if(!$xlsData){               
                    $this->error('无数据');
                }
		exportExcel($xlsName,$xlsCell,$xlsData); 
	}
	/**
     * 删除订单
     */
	 public function del(){   
	    $id=input("id");
	    $map['id']=array("in",$id);
		if(!$map["id"]){
			 $this->error("未选择数据！");
		}
		$res=db("Order")->where($map)->delete();
		if($res){
		   $this->success("删除成功！");
		}else{
			 $this->success("删除失败！");
		}
	}

}
