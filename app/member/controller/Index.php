<?php
/**
 * 首頁
 */
declare (strict_types = 1);
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\User;
use app\common\model\Article;
use app\common\model\Notice;
use app\common\model\Order;
use app\common\model\UserAccount;
use app\common\model\Stand;

class Index extends MemberBase
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
     * 显示后台
     */
    public function index()
    {
        if (!$this->isLogin()) {
            $this->redirect(url('@member/login'));
        } else {
            $id = session('user_info.id');
            $user = User::find($id);
            $menulist = getMenuData();

            return view('', [
                'user' => $user,
                'sidenav' => list_to_tree($menulist),//菜单
            ]);
        }
    }

    /**
     * 显示数据
     * @return \think\response\View
     */
    public function main()
    {
        $id = session('user_info.id');
        $user = User::find($id);
        $notice = Notice::limit(10)->order('id', 'desc')->select();
        $cloud = UserAccount::where('userid', $id)->count();
        $stand = Stand::where('uid', $id)->count();
        $article = Article::order('id', 'desc')->limit(8)->select();
        //现有云账户
        $vps = UserAccount::where('userid', $id)->order('id', 'desc')->limit(10)->select();
        foreach ($vps as $key => $value) {
            switch ($value['type']) {
                case 0:
                    $vps[$key]['type'] = '阿里云';
                    break;
                case 1:
                    $vps[$key]['type'] = '腾讯云';
                    break;
                case 2:
                    $vps[$key]['type'] = '华为云';
                    break;
                case 3:
                    $vps[$key]['type'] = 'AWS';
                    break;
                case 4:
                    $vps[$key]['type'] = 'GCP';
                    break;
                case 5:
                    $vps[$key]['type'] = 'Azure';
                    break;
            }
        }
        $time = time()+5*24*3600;
        $server = Stand::where('uid', $id)->whereTime('endtime', '<=', $time)->order('id', 'desc')->select();
        return view('', [
            'user' => $user,
            'cloud' => $cloud,
            'stand' => $stand,
            'notice' => $notice,
            'article' => $article,
            'vps' => $vps,
            'server' => $server
        ]);
    }
}
