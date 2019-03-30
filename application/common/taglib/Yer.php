<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 1010422715@qq.com All rights reserved.
// +----------------------------------------------------------------------
// | author 烟消云散 <1010422715@qq.com>
// +----------------------------------------------------------------------
namespace app\common\taglib;
use think\template\TagLib;
use  think\Db;
/**
 * 优惠券模型
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class Yer extends TagLib {

 /**
     * 定义标签列表
     */
      protected $tags   =  [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'close'     => ['attr' => 'time,format', 'close' => 0], //闭合标签，默认为不闭合
        'open'      => ['attr' => 'name,type', 'close' => 1], 
		'position'     => ['attr' =>'pos,name', 'close' => 1],

    ];

    /**
     * 这是一个闭合标签的简单演示
     */
    public function tagClose($tag)
    {
        $format = empty($tag['format']) ? 'Y-m-d H:i:s' : $tag['format'];
        $time = empty($tag['time']) ? time() : $tag['time'];
        $parse = '<?php ';
        $parse .= 'echo date("' . $format . '",' . $time . ');';
        $parse .= ' ?>';
        return $parse;
    }
	 /**
     * volist标签解析 循环输出数据集
     * 格式：
     * {volist name="userList" id="user" empty=""}
     * {user.username}
     * {user.email}
     * {/volist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
     /* 推荐位列表 */
    public function tagPosition($tag, $content){
        $pos    = $tag['pos'];
        $name   = $tag['name'];
        $parse = '<?php ';
       
        $parse .= '$__LIST__= ad(' . $pos  . ');';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'. $name .'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

}