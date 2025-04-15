<?php
/**
 * 杂项管理
 */
declare (strict_types = 1);
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\User;
use think\facade\Cache;
use think\exception\ValidateException;

class Tpl extends MemberBase
{
    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = ['message', 'lockscreen', 'clear'];

    /**
     * 消息
     * @return \think\response\View
     */
    public function message()
    {
        return view();
    }

    /**
     * 锁屏
     * @return \think\response\View
     */
    public function lockscreen()
    {
        $pwd = User::where('id', session('user_info.id'))->value('pass');
        $numbers = mt_rand(1, 4000);  //date("Ymd") % 4000每日
        $background = "http://img.infinitynewtab.com/wallpaper/" . $numbers . ".jpg";
        return view('', [
            'password' => $pwd,
            'background' => $background
        ]);
    }

    /**
     * 修改密码
     * @return \think\response\View
     */
    public function password()
    {
        if (request()->isPost()) {
            $data = input('param.');
            try {
                $this->validate($data, 'Admin.editpassword');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            $pwd = User::where('id', session('user_info.id'))->value('pass');
            if ($pwd !== md5($data['oldPsw'])) {
                return $this->error('原始密码输入错误');
            } else {
                $result = User::update(['pass' => md5($data['newpassword']), 'id' => session('user_info.id')]);;
                if ($result == true) {
                    return $this->success('修改密码成功');
                } else {
                    return $this->error('修改密码失败');
                }
            }
        } else {
            return view();
        }
    }

    /**
     * 清理运行缓存
     */
    public function clear()
    {
        if (request()->isGet()) {
            Cache::tag('agent')->clear();
            $this->success('清理系统缓存成功！');
        } else {
            $this->error('清理系统缓存失败！');
        }
    }
}
