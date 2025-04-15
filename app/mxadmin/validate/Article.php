<?php
/**
 * 规则参数
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\validate;

use think\Validate;

class Article extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'title' => 'require',
        'cid' => 'require',
        'intro' => 'require',
        'content' => 'require'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'title.require' => '标题不能为空！',
        'cid.require' => '文章分类不能为空！',
        'intro.require' => '文章介绍不能为空！',
        'content.require' => '文章内容不能为空！'
    ];
}