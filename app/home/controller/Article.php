<?php
/**
 * 文章详细
 */
namespace app\home\controller;
use app\home\HomeBase;
//加载数据模型
use app\common\model\Article as ArticleModel;
use app\common\model\Category;
use app\common\model\Cloud;
use app\common\model\Goods;

class Article extends HomeBase
{
	/**
	 * 文章详细
	 */
	public function index()
	{
		$name = $this->request->param('name', '', 'trim');
		$name = strtolower($name);
		$article = ArticleModel::where('alias', $name)->find();
		$cate = Category::where('id', $article['cid'])->find();
		if (!empty($article)) {
			//商品推荐
			$recommend[0] = Goods::order('id', 'desc')->limit(6)->select();
			$recommend[1] = Cloud::order('id', 'desc')->limit(6)->select();
			$rand = rand(0,1);
			return view('', [
				'info' => $article,
				'cate' => $cate,
				'recommend' => $recommend[$rand],
				'isrand' => $rand
			]);
		}else{
			$this->error('数据不存在！', 'https://www.kaihu123.com');
		}
	}
}