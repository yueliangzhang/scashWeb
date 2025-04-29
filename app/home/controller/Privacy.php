<?php
/**
 * 聯絡我們
 */
namespace app\home\controller;
use app\home\HomeBase;
//加载数据模型
use app\common\model\Custom;

class Privacy extends HomeBase
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