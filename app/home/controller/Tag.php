<?php
/**
 * 标签
 */
namespace app\home\controller;
use app\home\HomeBase;
//记载数据模型
use app\common\model\Tags;
use app\common\model\PageMap;
use app\common\model\Article;
use app\common\model\Cloud;
use app\common\model\Goods;
class Tag extends HomeBase
{
	/**
	 * 标签详细
	 */
	public function index($limit = 40)
	{
		$name = $this->request->param('name', '', 'trim');
		$tag = Tags::where('name', $name)->find();
		if (empty($tag)) {
			$this->error('标签不存在！');
		}

		//获取文章|服务器|云数据
		$list = PageMap::where('tag_id', $tag['id'])->order('id', 'desc')->limit(24)->select();
		foreach ($list as $key => $value) {
			$model = '';
			switch ($value['is_article']) {
				case 0://文章
					$model = new Article();
					break;
				case 1://服务器
					$model = new Goods();
					break;
				case 2:
					$model = new Cloud();
					break;
			}

			$list[$key]['child'] = $model->find($value['page_id']);
		}

		return view('', [
			'tag' => $tag,
			'list' => $list
		]);
	}
}