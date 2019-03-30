<?php
// +----------------------------------------------------------------------
// | Yershop 开源网店系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.yershop.com All rights reserved.
// +----------------------------------------------------------------------
namespace app\index\validate;
use think\Validate;
use think\Db;

class Comment extends Validate{
  protected $rule = [
        'doc_id'  =>  'require|max:225',
		
		
        
    ];

    protected $message = [
        'doc_id.require'  =>  'id必须',
        'content.require'  =>  '内容必须',
		
    ];

    protected $scene = [
        'add'   =>  ['doc_id','content'],
        'edit'  =>  ['doc_id','content'],
    ];   
	
}
