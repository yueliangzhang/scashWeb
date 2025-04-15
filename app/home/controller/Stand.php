<?php
/**
 * 物理机分类
 */
namespace app\home\controller;
use app\home\HomeBase;
use app\common\model\Category;
use app\common\model\Goods;

class Stand extends HomeBase
{
	/**
	 * 服务器分类
	 */
	public function index()
	{
		$name = $this->request->param('name', '', 'trim');
		$name = strtolower($name);
		if (empty($name)) {
			return $this->error('地址有误！');
		}

		$category = Category::where('alias', $name)->find();
		if (empty($category) || $category['pid'] != 59) {
			$this->error('页面不存在！', '/Server/');
		}
		
		//获取分类下面的子分类
		$catelist = Category::where('pid', $category['id'])->order('id', 'asc')->limit(6)->select();
		$list = [];
		foreach ($catelist as $key => $value) {
			$list[$key]['name'] = $value['name'];
			$stand = Goods::where('cid', $value['id'])->order('id', 'desc')->select();
			$list[$key]['stand'] = $stand;
		}

		return view('', [
			'category' => $category,
			'catelist' => $catelist,
			'list' => $list
		]);
	}
}