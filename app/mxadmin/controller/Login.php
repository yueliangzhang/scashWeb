<?php
/**
 * 后台登陆
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\controller;

use app\mxadmin\AdminBase;
use app\mxadmin\model\AdminModel;
use think\exception\ValidateException;
use think\captcha\facade\Captcha;

class Login extends AdminBase
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
     * 后台登录入口
     * @return \think\response\View
     */
    public function index()
    {
        if (request()->isPost()) {
            $data = input('param.');
            try {
                $this->validate($data, 'Login');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            $user = AdminModel::where([
                'username'  =>  $data['username'],
                'password'  =>  md5($data['password']),
            ])->find();

            if ($user == true) {
                $user['status'] == 0 && $this->error('该账号已被管理员禁用');
                $user_array = [
                    'admin_id' => $user['id'],
                    'username' => $user['username'],
                    'nickname' => $user['nickname'],
                    'is_admin' => $user['is_admin'],
                ];
                session('admin_info', $user_array);
                session('admin_sign', data_sign($user_array));

                $result = AdminModel::update([
                    'login_ip'     => request()->ip(),
                    'login_time'   => time(),
                    'login_times'    => $user['login_num'] + 1,
                ], ['id' => $user['id']]);

                if ($result == true) {
                    sysoplog('用户登录', "登录系统后台成功");
                    return $this->success('登录成功，请稍候');
                } else {
                    return $this->error('登录失败');
                }
            } else {
                return $this->error('账号或密码输入错误');
            }
        } else {
            $this->isLogin() && $this->redirect(url('@mxadmin'));
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
        return Captcha::create("verify");
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $user = AdminModel::where('id', getAdminId())->find();
        AdminModel::update([
            'last_login_ip'     => $user['login_ip'],
            'last_login_time'   => $user['login_time'],
        ], ['id' => $user['id']]);

        // 删除Session
        session('admin_info', null);
        session('admin_sign', null);
        return $this->redirect(url('@mxadmin/login'));
    }
}
