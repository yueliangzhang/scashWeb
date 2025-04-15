<?php
/**
 * 联系我们
 */
namespace app\spider\controller;
use app\spider\SpiderBase;
//加载数据模型
use app\common\model\Custom;

class Contact extends SpiderBase
{
	/**
	 * 联系我们
	 */
	public function index()
	{
		$list = Custom::order('id', 'desc')->select();
		
		return $this->result($list->toArray(), 200, 'OK');
	}
}