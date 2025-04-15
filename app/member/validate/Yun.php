<?php
/**
 * 账户
 */
declare (strict_types = 1);
namespace app\member\validate;
use think\Validate;

class Yun extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'type' => 'require|number',
        'email' => 'require|email',
        'usdtype' => 'require|number',
        'num' => 'require|number',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'type.require' => '请选择账户类型',
        'type.number' => '账户类型有误',
        'email.require' => '邮箱不能为空！',
        'email.email' => '邮箱格式有误！',
        'usdtype.require' => '请选择充值USDT类型',
        'usdtype.number' => 'USDT账户类型有误',
        'num.require' => '充值数量为空！',
        'num.number' => '请填写正整数'
    ];

    /**
     * 场景验证定义
     * @var array
     */
    protected $scene = [
        'addUser'  =>  ['type', 'email', 'usdtype', 'num'],
        'recharge' => ['usdtype', 'num'],
    ];
}