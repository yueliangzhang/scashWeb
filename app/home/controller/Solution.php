<?php
/**
 * 文章列表
 */
namespace app\home\controller;
use app\home\HomeBase;
//加载数据模型
use app\common\model\Article;
use app\common\model\Category;

class Solution extends HomeBase
{
	/**
	 * 全展示
	 */
	public function index($limit = 40)
	{
        $seo = [
        	'title' => '云计算基础知识与技术',
        	'description' => '探索人工智能与数据分析技术，包括机器学习模型训练（如SageMaker、Vertex AI）、大数据分析解决方案（如BigQuery、Synapse）以及数据可视化工具（如Looker、QuickSight）。',
        	'info' => '了解云网络与安全的关键概念，涵盖VPC、子网、DNS、负载均衡、CDN等技术，保障您的云服务安全。还包括IAM、KMS、SSL/TLS加密与DDoS防护等合规管理措施。'
        ];

        $page = $this->request->param('page', '', 'intval');
        $list = Article::order('id', 'desc')->paginate($limit);
        //获取内容图片
        foreach ($list as $key => $value) {
        	$cate = Category::find($value['cid']);
        	$list[$key]['classname'] = $cate['name'];
        	$list[$key]['cate'] = $cate['alias'];
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $value["content"], $match);
            $default = rand(1, 5);
            $list[$key]['headimg'] = isset($match[1][0])?$match[1][0]:"/static/aliyun/images/".$default.".png";
        }

        return view('', [
        	'seo' => $seo,
        	'list' => $list
        ]);
	}

	/**
	 * 文章分类
	 */
	public function class($limit = 20)
	{
		$page = $this->request->param('page', '', 'intval');
		$name = $this->request->param('name', '', 'trim');
		$name = strtolower($name);
		$cate = Category::where('alias', $name)->find();
		$parentCate = Category::find($cate['pid']);
		$cate['parentname'] = $parentCate['name'];
		if (!empty($cate)) {
			//获取文章
			$list = Article::where('cid', $cate['id'])->order('id', 'desc')->paginate($limit);
			//获取内容图片
	        foreach ($list as $key => $value) {
	        	$cate = Category::find($value['cid']);
	        	$list[$key]['classname'] = $cate['name'];
	        	$list[$key]['cate'] = $cate['alias'];
	            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
	            preg_match_all($pattern, $value["content"], $match);
	            $default = rand(1, 5);
            	$list[$key]['headimg'] = isset($match[1][0])?$match[1][0]:"/static/aliyun/images/".$default.".png";
	        }

			return view('list', [
				'cate' => $cate,
				'list' => $list
			]);
		}else{
			$this->error('页面不存在！');
		}
	}
}