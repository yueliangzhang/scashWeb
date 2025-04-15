<?php
/**
 * 杂项管理
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\controller;

use app\mxadmin\AdminBase;
use app\mxadmin\model\AdminModel;
use think\facade\Cache;
use think\exception\ValidateException;

class Tpl extends AdminBase
{
    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = ['message', 'lockscreen', 'clear', 'theme'];

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
        $pwd = AdminModel::where('id', getAdminId())->value('password');
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
            $pwd = AdminModel::where('id', getAdminId())->value('password');
            if ($pwd !== md5($data['oldPsw'])) {
                return $this->error('原始密码输入错误');
            } else {
                $result = AdminModel::update(['password' => $data['newpassword'], 'id' => getAdminId()]);;
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
            Cache::tag('admin')->clear();
            $this->success('清理系统缓存成功！');
        } else {
            $this->error('清理系统缓存失败！');
        }
    }

    /**
     * 主题设置
     * @return \think\response\View
     */
    public function theme()
    {
        return view();
    }
}
