<?php
namespace app\index\model;
use think\Model;
use think\Db;
/**
 * 分类模型
 */
class Document extends Model{
  
     protected $auto = ["create_time","update_time","uid"];
     protected $insert = ["create_time","update_time","uid"];  
     protected $update = ["create_time","update_time","uid"];  
     protected function setCreateTimeAttr(){
		   $create_time  = input('create_time');
		   return $create_time?strtotime($create_time):time();
		
    }
	protected function setUidAttr(){
		return is_login();
    }
    protected function setUpdateTimeAttr(){
		
		   $update_time  = input('update_time');
		   return $update_time?strtotime($update_time):time();
		
    }
    /**
     * 获取详情页数据
     * @param  integer $id 文档ID
     * @return array       详细数据
     */
    public function getInfo($id){
        /* 获取基础数据 */
        $info =Db::name("Document")->field(true)->find($id);
        if(!(is_array($info) || 1 !== $info['status'])){
            $this->error = '文档被禁用或已删除！';
            //return false;
        }
        /* 获取模型数据 */
		$name  =get_models($info["model_id"],'name');
		$merge=Db::name($name)->find($id);
        if($merge){
			$info = array_merge($info, $merge);
		}
        return $info;
    }

    /**
     * 新增或更新一个文档
     */
    public function updatePost($id = null){
  
        /* 添加或新增扩展内容 */
        $class = $this->getClass($_POST['model_id']);
        if (empty($_POST['id'])) {//新增数据
            $_POST['id'] = $id;
            $id =$class->allowField(true)->save($_POST);
            if (!$id) {
				 $map["id"]=$id;
				 Db::name("Document")->delete($id);
                 $this->error = '新增数据失败！';
                 return false;
            }
        } else { //更新数据
			$map["id"]=$id;
            $status = $class->allowField(true)->save($_POST,['id' =>$id]);
            if (false === $status) {
                $this->error = '更新数据失败！';
                return false;
            }
        }
        return true;
		
    }
    /**
     * 获取扩展模型对象
     * @param  integer $model 模型编号
     * @return object         模型对象
     */
    private function down($id){
        $url = "./public/images/2222.jpg";
		$file=fopen($url,"r");
		header("Content-Type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".filesize($url));
		header("Content-Disposition: attachment; filename=ceshi.jpg");
		echo fread($file,filesize($url));
		fclose($file);
    }
    /**
     * 获取扩展模型对象
     * @param  integer $model 模型编号
     * @return object         模型对象
     */
    private function getClass($id){
        $name  =get_models($id,'name');
		$class=substr($name,0,1);
		$name=strtoupper($class).substr($name,1);
        $class =  'app\admin\model\\' . $name;
        return new $class;
    }

    public function getError(){
       
        return $this->error;
    }
}
