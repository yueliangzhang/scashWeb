<?php
/**
 * 独服列表
 */
namespace app\home\controller;
use app\home\HomeBase;
//加载数据模型
use app\common\model\Category;
use app\common\model\Goods;
use app\common\model\Article;

class Server extends HomeBase
{
	/**
	 * 展示
	 */
	public function index($limit = 32)
	{
		$child = Category::where('pid', 59)->column('id');
		$page = $this->request->param('page', '', 'intval');
        $allyun = implode(',', $child);
        $nowdata = Category::where('pid', 'in', $allyun)->column('id');
        $all = implode(',', $nowdata);
		$seo = [
			'title' => '独立服务器/物理机列表',
			'description' => '提供香港VPS服务器、美国云服务器、新加坡云服务器与裸金属服务器的租赁服务。我们的服务器高效、稳定，支持GPU加速，适用于全球客户的各类需求。',
			'info' => '全球范围内的服务器租赁服务，涵盖香港、美国、新加坡的云服务器与裸金属服务器，支持GPU需求，满足大数据、AI、视频处理等高性能计算需求。'
		];

		//所有云产品
		$list = Goods::where('cid', 'in', $all)->order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$cate = Category::find($value['cid']);
			$list[$key]['classname'] = $cate['name'];
			$list[$key]['cate'] = $cate['alias'];
			$list[$key]['cname'] = str_replace('-', ' ', $value['alias']);
		}

		return view('', [
			'seo' => $seo,
			'list' => $list
		]);
	}

	//分类展示
	public function class($limit = 24)
	{
		$page = $this->request->param('page', '', 'intval');
		$name = $this->request->param('name', '', 'trim');
		$name = strtolower($name);
		$category = Category::where('alias', $name)->find();
		if (!empty($category)) {
			$parentCate = Category::find($category['pid']);
			$category['parentname'] = $parentCate['name'];
			if ($parentCate['pid'] != 59) {
				$this->error('页面不存在！', '/Cloud/');
			}
			$list = Goods::where('cid', $category['id'])->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$cate = Category::find($value['cid']);
				$list[$key]['classname'] = $cate['name'];
				$list[$key]['cate'] = $cate['alias'];
				$list[$key]['cname'] = str_replace('-', ' ', $value['alias']);
			}
			//获取文章
			$article = Article::where('cid', $category['id'])->order('id', 'desc')->limit(10)->select();
			//获取内容图片
	        foreach ($article as $key => $value) {
	        	$cate = Category::find($value['cid']);
	        	$article[$key]['classname'] = $cate['name'];
	        	$article[$key]['cate'] = $cate['alias'];
	            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
	            preg_match_all($pattern, $value["content"], $match);
	            $article[$key]['headimg'] = isset($match[1][0])?$match[1][0]:"/static/aliyun/images/1.png";
	        }
			return view('list', [
				'cate' => $category,
				'list' => $list,
				'article' => $article
			]);
		}else{
			$this->error('页面不存在！');
		}
	}
}