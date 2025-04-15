<?php
/**
 * 登陆
 * @author tigger
 */
declare (strict_types = 1);
namespace app\member\validate;
use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'nickname' => 'require',
        'email' => 'require|email',
        'password' => 'require|length:6,20',
        'wechat' => 'require',
        'captcha' => 'require|captcha',
        'old_password' => 'require|length:6,20',
        'captchaCode' => 'require',
        'mobile' => 'require|mobile'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'nickname.require' => '用户昵称不能为空！',
        'email.require' => '邮箱不能为空！',
        'email.email' => '请输入正确的邮箱格式！',
        'password.require' => '登录密码不能为空！',
        'password.length' => '登录密码必须6到20个字符！',
        'wechat.require' => '请输入微信账户',
        'old_password.require' => '老密码不能为空！',
        'old_password.length' => '老密码必须6到20个字符！',
        'captcha.require' => '验证码不能为空！',
        'captcha.captcha' => '验证码错误，请重新输入！',
        'captchaCode.require' => '邮箱验证码不能为空！',
        'mobile.require' => '手机不能为空！',
        'mobile.mobile' => '手机号码不正确',

    ];

    /**
     * 场景验证定义
     * @var array
     */
    protected $scene = [
        'login'  =>  ['email', 'password', 'captcha'],
        'register' => ['nickname', 'email', 'wechat', 'password', 'captchaCode'],
        'chpass' => ['old_password', 'password'],
        'captchadata' => ['email'],
        'Manydata' => ['nickname', 'wechat', 'mobile'],
    ];
}
