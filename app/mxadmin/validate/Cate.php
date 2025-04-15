<?php
/**
 * 规则参数
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\validate;

use think\Validate;

class Cate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'name' => 'require',
        'alias' => 'require',
        'sort' => 'number',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.require' => '分类名称不能为空！',
        'alias.require' => '英文别名不能为空!',
        //'name.chsAlphaNum' => '分类名称只能是汉字、字母和数字！',
        'sort.number' => '排序权重只能是数字！',
    ];
}