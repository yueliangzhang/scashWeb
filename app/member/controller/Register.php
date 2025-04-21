<?php
/**
 * 注册
 */
declare (strict_types = 1);
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\User;
use think\exception\ValidateException;
//use think\captcha\facade\Captcha;
use app\common\controller\Email;

class Register extends MemberBase
{
	/**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = ['index', 'captcha'];

    /**
     * 注册入口
     * @return \think\response\View
     */
    public function index()
    {
    	if (request()->isPost()) {
    		$data = [
                'nickname' => $this->request->param('nickname', '', 'trim'),
                'email' => $this->request->param('email', '', 'trim'),
                'wechat' => $this->request->param('wechat', '', 'trim'),
                'password' => $this->request->param('password', '', 'trim'),
                'captchaCode' => $this->request->param('captchaCode', '', 'trim'),
            ];

            try {
                $this->validate($data, 'User.register');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }

            //验证邮箱验证码
            $captcha = session('EmailCode');
            if (!$captcha || $captcha['email'] != $data['email'] || $captcha['captcha'] != $data['captchaCode'] || $captcha['expire_time'] < time()) {
                return $this->error('邮箱验证码已失效！');
            }
            //判断email是否存在
            $user = User::where('email', $data['email'])->find();
            if (!empty($user)) {
                return $this->error('邮箱已存在');
            }

            $data['pass'] = md5($data['password']);
            $data['addtime'] = time();
            $result = User::create($data);
            if ($result == true) {
                return $this->success('注册成功，请登录');
            } else {
                return $this->error('注册失败');
            }
        }else{
            return view('', [
                'background' => 'https://open.saintic.com/api/bingPic/?picSize=2'
            ]);
        }
    }

    /**
     * 生成邮箱验证码
     * @return \think\Response
     */
    public function captcha()
    {
        if (request()->isPost()) {
            $data = [
                'email' => $this->request->param('email', '', 'trim')
            ];

            try {
                $this->validate($data, 'User.captchadata');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            $captcha = mt_rand(100000, 999999);
            // 存储验证码到session
            session('EmailCode', [
                'email' => $data['email'],
                'captcha' => $captcha,
                'expire_time' => time() + 300 // 5分钟有效期
            ]);
            $subject = '【T1 cloud开户网】 新用户邮箱验证';
            $body = '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>邮箱验证码</title>
    <style>
        /* 邮件的基础样式 */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .email-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .email-header h1 {
            font-size: 24px;
            color: #007BFF;
        }
        .email-content {
            font-size: 16px;
            line-height: 1.6;
        }
        .email-content p {
            margin: 10px 0;
        }
        .email-code {
            display: block;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #007BFF;
            margin: 20px 0;
            background: #f0f8ff;
            padding: 10px;
            border-radius: 5px;
        }
        .email-footer {
            font-size: 12px;
            color: #888;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- 邮件头部 -->
        <div class="email-header">
            <h1>T1 cloud邮箱验证码</h1>
        </div>

        <!-- 邮件内容 -->
        <div class="email-content">
            <p>亲爱的用户，</p>
            <p>您正在进行邮箱验证，请使用以下验证码完成操作：</p>
            <span class="email-code">'.$captcha.'</span>
            <p>验证码有效期为 <strong>5分钟</strong>，请勿告诉他人。如非本人操作，请忽略此邮件。</p>
        </div>

        <!-- 邮件底部 -->
        <div class="email-footer">
            <p>感谢您的使用！</p>
            <p>本邮件由系统自动发送，请勿直接回复。</p>
        </div>
    </div>
</body>
</html>
';


            $email = new Email();
            $result = $email->sendmail($data['email'], $subject, $body);

            if ($result == true) {
                return $this->success('发送成功！');
            }else{
                return $this->error('发送失败');
            }
        }
    }
}