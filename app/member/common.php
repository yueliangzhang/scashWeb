<?php
// 这是系统自动生成的公共文件
if (!function_exists('getMenuData')) {
	/**
	 * 客户菜单
	 */
	function getMenuData()
	{
		$menulist = [
			//客户中心
			0 => [
				'id' => 1,
				'pid' => 0,
				'name' => '',
				'title' => '个人中心',
				'icon' => 'layui-icon layui-icon-username'
			],
			1 => [
				'id' => 2,
				'pid' => 1,
				'name' => '/member/user/index',
				'title' => '个人信息',
				'icon' => '',
				'open' => 1
			],
			5 => [
				'id' => 6,
				'pid' => 0,
				'name' => '',
				'title' => '产品管理',
				'icon' => 'layui-icon layui-icon-gift'
			],
			2 => [
				'id' => 3,
				'pid' => 6,
				'name' => '/member/yun/index',
				'title' => '公有云管理',
				'icon' => '',
				'open' => 1
			],
			6 => [
				'id' => 7,
				'pid' => 6,
				'name' => '/member/stand/index',
				'title' => '服务器管理',
				'icon' => '',
				'open' => 1
			],
			3 => [
				'id' => 4,
				'pid' => 0,
				'name' => '',
				'title' => '账单中心',
				'icon' => 'layui-icon layui-icon-form',
				'open' => 1
			],
			4 => [
				'id' => 5,
				'pid' => 4,
				'name' => '/member/order/index',
				'title' => '购买订单',
				'icon' => '',
				'open' => 1
			],
			7 => [
				'id' => 8,
				'pid' => 4,
				'name' => '/member/refund/index',
				'title' => '退款订单',
				'icon' => '',
				'open' => 1
			]
		];

		return $menulist;
	}
}