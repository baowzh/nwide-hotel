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
use think\Controller;
use think\Db;
class Rules extends Admin{

    public function index(){     
        if($_POST){
			foreach ($_POST as $key=>$value){
				if($value){
					$map[$key]  = array('like', '%'.$value.'%');
				 }
			}
        }
		$map=isset($map)?$map:'';
        $res=getLists('Rules',$map,10,'id desc',"");
	    $this->assign('res', $res);
		$this->meta_title="采集规则管理";
		$this->assign('meta_title', $this->meta_title);
	    return $this->fetch(); 
	}

	public function edit($id){   
	    if($_POST){
		   $Rules =new \app\admin\model\Rules;;
           $res=$Rules->allowField(true)->validate(true)->save($_POST,['id' => $_POST['id']]);
	       if($res){
			  addUserLog("edit_Rules",session_uid());
		      $this->success("更新成功！",cookie("__forward__"));
		   }else{
			  $error=$Rules->getError()?$Rules->getError():"更新失败！";
			  $this->error($error);
		   } 
	  }
	  else{
		 $map['id']=$id;
		 $info=db("Rules")->where($map)->find();
	     $this->assign('info', $info);
		   cookie("__forward__",input('server.HTTP_REFERER'));
		 $this->meta_title="编辑采集规则";
		 $this->assign('meta_title', $this->meta_title);
	     return $this->fetch();
	   }
	}

	public function add($id=""){  
	   if($_POST){
		  $Rules =new \app\admin\model\Rules;;
            // 过滤post数组中的非数据表字段数据
          $res=$Rules->validate(true)->allowField(true)->save($_POST);
	      if($res){
			  addUserLog("add_Rules",session_uid());
		      $this->success("新增成功！",cookie("__forward__"));
		  }else{
			  $error=$Rules->getError()?$Rules->getError():"新增失败！";
			  $this->error($error);
		  } 
	   }
	   else{
		  cookie("__forward__",input('server.HTTP_REFERER'));
		  $this->meta_title="新增采集规则";
		  $this->assign('meta_title', $this->meta_title);
	      return $this->fetch("Rules/edit");
	   }
	}

	public function del(){   
	    $id=input("id");
	    $map['id']=array("in",$id);
		if(!$map["id"]){
			 $this->error("未选择数据！");
		}
		$res=db("Rules")->where($map)->delete();
		if($res){
		   addUserLog("del_Rules",session_uid());
		   $this->success("删除成功！");
		}else{
			 $this->error("删除失败！");
		}
	}

	public function get(){  
		$p=isset($_GET['p'])?$_GET['p']:1;
	    if($p<=8){
           
			$conn=file_get_contents("http://travel.cnr.cn/2011lvpd/gny/news/index_$p.html");//获取页面内容
			$pattern="/<li>
                        <a class='scale pic' href=\"(.*)\">
                            <img src='http://www.cnr.cn/lvyou/list/20180109/W020180109333925469836.png'>
                        </a>						
                        <div class='text'>
                            <strong>
                                <a href='(.*)' target='_blank'>(.*)</a>
                            </strong>
                            <p>阿坝州正在研究九寨沟门票价格，希望通过重建让景区回归国家公园风景名胜区的公益属性。下一步将出台旅游振兴发展意见，进一步明确重建要求和发展阶段，给予市场明确的预期和引导。</p>
                        </div>
                        <span class='publishTime'>2018-01-09  09:15</span>
                    </li>/";//正则
			//$arr=array();
			preg_match_all($pattern, $conn, $arr);//匹配内容到arr数组 
			addUserLog(var_export($arr,true),1);
			foreach ($arr[1] as $key => $value) {//二维数组[2]对应id和[1]刚好一样,利用起key
				$url="http://www.ce.cn/cysc/sp/jiu/index_".$arr[2][$key];
				$sql="insert into list(title,url) value ('$value', '$url')";
				$data["title"]=$value;
				$data["create_time"]=time();
				$data["url"]=$url;
				$res=db("rules")->insert($data);
				echo "<a href='content.php?url=http://www.93moli.com/$url'>$value</a>"."<br/>";
			}
			 $p++;
			 addUserLog(var_export($arr,true),1);
			 //echo "正在采集URL数据列表$p...请稍后...";
			 $url=url("get?p=".$p);
			 //echo "<script>window.location='$url'</script>";

		 }else{
			 echo "采集数据结束。";
		 }
	}
}
