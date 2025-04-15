<?php
/**
 * 云计算分类
 */
namespace app\home\controller;
use app\home\HomeBase;
use app\common\model\Category;
use app\common\model\Cloud;

class Computing extends HomeBase
{
	/**
	 * 云计算分类
	 */
	public function index()
	{
		$name = $this->request->param('name', '', 'trim');
		$name = strtolower($name);
		if (empty($name)) {
			return $this->error('地址有误！');
		}

		$category = Category::where('alias', $name)->find();
		if (empty($category) || $category['pid'] != 80) {
			$this->error('页面不存在！', '/Cloud/');
		}
		
		//获取分类下面的子分类
		$catelist = Category::where('pid', $category['id'])->order('id', 'asc')->limit(6)->select();
		$list = [];
		foreach ($catelist as $key => $value) {
			$list[$key]['name'] = $value['name'];
			$cloud = Cloud::where('cid', $value['id'])->order('id', 'desc')->select();
			$list[$key]['cloud'] = $cloud;
		}

		return view('', [
			'category' => $category,
			'catelist' => $catelist,
			'list' => $list
		]);
	}
}