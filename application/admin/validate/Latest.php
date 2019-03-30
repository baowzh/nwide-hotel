<?php
// +----------------------------------------------------------------------
// | Yershop 开源网店系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.yershop.com All rights reserved.
// +----------------------------------------------------------------------
namespace app\admin\validate;
use think\Validate;
use think\Db;

class Latest extends Validate{
  protected $rule = [
        'title'  =>  'require|max:25',
		'src'  =>  'require|max:225', 
    ];

    protected $message = [
        'title.require'  =>  '名称必须',
        'src.require'  =>  '图片必须',
    ];

    protected $scene = [
        'add'   =>  ['title','src'],
        'edit'  =>  ['title','src'],
    ];   
	
}
