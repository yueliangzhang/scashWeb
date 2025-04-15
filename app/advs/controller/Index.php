<?php
/**
 * 首页
 */
namespace app\advs\controller;
use app\advs\AdvsBase;
use app\common\model\User;

class Index extends AdvsBase
{
    /**
     * 首页展示
     */
    public function index()
    {
        //获取注册用户
        $user = User::order('id', 'desc')->select();
        return view('', [
            'user' => $user
        ]);
    }
}