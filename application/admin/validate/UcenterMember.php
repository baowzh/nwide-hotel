<?php
// +----------------------------------------------------------------------
// | Yershop 开源网店系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.yershop.com All rights reserved.
// +----------------------------------------------------------------------
namespace app\admin\validate;
use think\Validate;
use think\Db;

class UcenterMember extends Validate{
  protected $rule = [
        'username'  =>  'require|max:225',
		'password'  =>  'require|max:225', 
    ];

    protected $message = [
        'username.require'  =>  '名称必须',
        'password.require'  =>  '密码必须',
    ];

    protected $scene = [
        'add'   =>  ['username','password'],
        'edit'  =>  ['username','password'],
    ];   
	
}
