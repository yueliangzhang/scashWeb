<?php
/**
 * 登录
 */
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\User;
use think\exception\ValidateException;
use think\captcha\facade\Captcha;

class Login extends MemberBase
{
	/**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = ['index', 'captcha'];

    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = ['logout'];

    /**
     * 登录入口
     * @return \think\response\View
     */
    public function index()
    {
        if (request()->isPost()) {
            $data = input('param.');
            try {
                $this->validate($data, 'User.login');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }

            $user = User::where([
                'email'  =>  $data['email'],
                'pass'  =>  md5($data['password']),
            ])->find();

            if ($user == true) {
                $user['status'] == 0 && $this->error('该账号已被管理员禁用');
                $user_array = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'nickname' => $user['nickname'],
                    'is_admin' => 1,
                ];
                session('user_info', $user_array);
                session('user_sign', data_sign($user_array));

                $result = User::update([
                    'loginip'     => request()->ip(),
                    'logintime'   => time()
                ], ['id' => $user['id']]);

                if ($result == true) {
                    return $this->success('登录成功，请稍候');
                } else {
                    return $this->error('登录失败');
                }

            } else {
                return $this->error('账号或密码输入错误');
            }
        } else {
            $this->isLogin() && $this->redirect(url('@member'));
            return view('', [
                'background' => 'https://open.saintic.com/api/bingPic/?picSize=2'
            ]);
        }
    }

    /**
     * 生成验证码
     * @return \think\Response
     */
    public function captcha()
    {
        return Captcha::create("memberVerify");
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        // 删除Session
        session('user_info', null);
        session('user_sign', null);
        return $this->redirect(url('@member/login'));
    }
}