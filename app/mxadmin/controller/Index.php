<?php
/**
 * 后台首頁
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;

class Index extends AdminBase
{
    /**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = ['index'];

    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = ['index', 'main'];

    /**
     * 显示后台首頁
     * @return \think\response\View
     */
    public function index()
    {
        if (!$this->isLogin()) {
            $this->redirect(url('@mxadmin/login'));
        } else {
            return view('index', [
                'sidenav'   =>  list_to_tree(getMenuData()),
                'admininfo'  =>  session('admin_info'),
            ]);
        }
    }

    /**
     * 显示工作台
     * @return \think\response\View
     */
    public function main()
    {
        //当前用户数
        $user = \app\common\model\User::count();
        $cloud = \app\common\model\UserAccount::count();
        $stand = \app\common\model\Stand::count();

        //当月数据
        $monthUser = \app\common\model\User::whereMonth('addtime')->count();
        $monthCloud = \app\common\model\UserAccount::whereMonth('addtime')->count();
        $monthStand = \app\common\model\Stand::whereMonth('addtime')->count();

        $tongji = [
            'user' => $user,
            'cloud' => $cloud,
            'stand' => $stand,
            'monthUser' => $monthUser,
            'monthCloud' => $monthCloud,
            'monthStand' => $monthStand
        ];

        //未处理订单
        $order = \app\common\model\Order::where('status', 1)->order('id', 'desc')->select();

        //到期服务器
        $time = time()+5*24*3600;
        $stand = \app\common\model\Stand::whereTime('endtime', '<=', $time)->order('id', 'asc')->select();

        //文章列表
        $article = \app\common\model\Article::order('id', 'desc')->limit(10)->select();
        $notice = \app\common\model\Notice::order('id', 'desc')->limit(10)->select();

        
        return view('', [
            'tongji' => $tongji,
            'stand' => $stand,
            'order' => $order,
            'article' => $article,
            'notice' => $notice
        ]);
    }
}
