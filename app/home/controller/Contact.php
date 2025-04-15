<?php
/**
 * 联系我们
 */
namespace app\home\controller;
use app\home\HomeBase;
//加载数据模型
use app\common\model\Custom;

class Contact extends HomeBase
{
	/**
	 * 展示
	 */
	public function index()
	{
		$list = Custom::order('id', 'desc')->select();
		return view('', [
			'list' => $list
		]);
	}
}