<?php
// +----------------------------------------------------------------------
// | Yershop 开源网店系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.yershop.com All rights reserved.
// +----------------------------------------------------------------------
namespace app\admin\validate;
use think\Validate;
use think\Db;

class Menu extends Validate{
  protected $rule = [
        'name'  =>  'require|max:25',
		
        
    ];

    protected $message = [
        'name.require'  =>  '名称必须',
        
    ];

    protected $scene = [
        'add'   =>  ['name'],
        'edit'  =>  ['name'],
    ];   
	
}
