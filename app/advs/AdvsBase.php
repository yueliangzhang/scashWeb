<?php
/**
 * 前端初始化
 */
namespace app\advs;
use app\BaseController;
use think\facade\View;

class AdvsBase extends BaseController
{
	/**
     * 类初始化
     */
    public function initialize()
    {
        $this->getSysconfig();
        $this->getlinks();
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
    	// 获取SEO信息 阿里云默认ID是8
    	$native = \app\common\model\Category::order('id', 'asc')->select();
        View::assign([
            'sysconf' => \app\mxadmin\model\Config::getConfigData('system'),
            'setconfig' => \app\mxadmin\model\Config::getConfigData('setconfig'),
            'setcontact' => \app\mxadmin\model\Config::getConfigData('setcontact'),
            'islogin' => $this->isLogin() ? 1 : 0,
            'nav' => list_to_tree($native->toArray()),
            'control' => strtolower(request()->controller())
        ]);
    }

    /**
     * 获取其他参数
     */
    public function getlinks()
    {
        $links = \app\common\model\Links::where('type', 1)->order('id', 'desc')->select();
        $custom = \app\common\model\Custom::where('status', 1)->order('id', 'asc')->select();
        View::assign([
            'links' => $links,
            'custom' => $custom
        ]);
    }
}