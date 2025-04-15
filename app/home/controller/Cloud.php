<?php
/**
 * 公有云
 */
namespace app\home\controller;
use app\home\HomeBase;
//加载数据模型
use app\common\model\Cloud as CloudModel;
use app\common\model\Category;
use app\common\model\Article;

class Cloud extends HomeBase
{
	//首頁
	public function index($limit = 32)
	{
		$child = Category::where('pid', 80)->column('id');
		$page = $this->request->param('page', '', 'intval');
        $allyun = implode(',', $child);
        $nowdata = Category::where('pid', 'in', $allyun)->column('id');
        $all = implode(',', $nowdata);
		$seo = [
			'title' => '全球云服务产品代理开户',
			'description' => '从阿里云国际到AWS、GCP、Azure等全球云服务，查看我们的云产品列表，找到适合您需求的云平台。我们为全球客户提供云服务的代理开户与充值支持。',
			'info' => '在我们的云产品列表页面，您可以全面了解阿里云国际、腾讯云国际、华为云国际、AWS、GCP、Azure等全球领先云平台的各种服务。帮助您根据需求选择最合适的云产品，并通过我们获得专业的代理开户与充值服务。'
		];

		//所有云产品
		$list = CloudModel::where('cid', 'in', $all)->order('id', 'desc')->paginate($limit);
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
			if ($parentCate['pid'] != 80) {
				$this->error('页面不存在！', '/Server/');
			}
			$category['parentname'] = $parentCate['name'];
			$list = CloudModel::where('cid', $category['id'])->order('id', 'desc')->paginate($limit);
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