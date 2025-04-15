<?php
/**
 * 预处理文件
 */
namespace app\member;
use app\BaseController;
use think\facade\View;

class MemberBase extends BaseController
{
	/**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = ['login'];

    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = [];


	/**
     * 类初始化
     */
    public function initialize()
    {
        $this->getSysconfig();
        !$this->checkLogin() && $this->error('登录超时，请重新登录', url('/member/login'));
    }

    /**
     * @return bool
     */
    public function checkLogin()
    {
        if (!$this->isLogin() && !in_array(strtolower(request()->action()), $this->noNeedLogin)) {
            return false;
        }

        return true;
    }

    /**
     * 检测是否登录
     * @return bool
     */
    public function isLogin()
    {
        $admin = session('user_info');
        if (empty($admin)) {
            return false;
        } else {
            return session('user_sign') == data_sign($admin) ? true : false;
        }
    }

    /**
     * 获取配置
     * @return array
     */
    public function getSysconfig()
    {
        View::assign([
            'sysconf' => \app\mxadmin\model\Config::getConfigData('system'),
            'setconfig' => \app\mxadmin\model\Config::getConfigData('setconfig')
        ]);
    }
}