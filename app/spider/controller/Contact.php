<?php
/**
 * 聯絡我們
 */
namespace app\spider\controller;
use app\spider\SpiderBase;
//加载数据模型
use app\common\model\Custom;

class Contact extends SpiderBase
{
	/**
	 * 聯絡我們
	 */
	public function index()
	{
		$list = Custom::order('id', 'desc')->select();
		
		return $this->result($list->toArray(), 200, 'OK');
	}
}