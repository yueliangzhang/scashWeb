<?php
/**
 * Sitemap生成
 */
namespace app\sync\controller;
use app\BaseController;
use think\facade\Db;
use app\common\controller\Sitemap;

class Sitemaps extends BaseController
{
	/**
	 * 自动生成 每天生成一次
	 */
	public function index()
	{
		for ($i = 0; $i < 1; $i++) { 
			$this->map($i);
		}
	}

	/**
	 * 单个sitemap
	 */
	private function map($num = 0)
	{
		//初始化sitemap
		$sitemap = new Sitemap("https://www.kaihu123.com", "sitemap".($num?$num:""));
		//添加项目
		$sitemap->addItem('/', '1.0', 'always', 'Today');
		//添加给公有云流程
		$sitemap->addItem('/Cloud/', '0.8', 'daily', date('Y-m-d'));
		//云品牌
		$compute = Db::name('category')->where('pid', 80)->order('id', 'asc')->select();
		foreach ($compute as $key => $value) {
			$sitemap->addItem('/Computing/'.$value['alias'].'/', '0.8', 'weekly', date('Y-m-d'));
		}
		//云品牌详细产品
		$cloudcate = Db::name('category')->where('pid', 'in', '8,9,10,11,12,13')->order('id', 'desc')->select();
		foreach ($cloudcate as $key => $value) {
			$sitemap->addItem('/Cloud/'.$value['alias'].'/', '0.8', 'weekly', date('Y-m-d'));
		}
		$cloud = Db::name('cloud')->order('id', 'desc')->select();
		foreach ($cloud as $key => $value) {
			$sitemap->addItem('/vps/'.$value['alias'].'/', '0.5', 'monthly', date('Y-m-d'));
		}
		//服务器
		$sitemap->addItem('/Server/', '0.8', 'daily', date('Y-m-d'));
		//服务器分类
		$stand = Db::name('category')->where('pid', 59)->order('id', 'asc')->select();
		foreach ($stand as $key => $value) {
			$sitemap->addItem('/Dedicated/'.$value['alias'].'/', '0.8', 'weekly', date('Y-m-d'));
		}
		$servercate = Db::name('category')->where('pid', 'in', '2,3,4,18')->order('id', 'desc')->select();
		foreach ($servercate as $key => $value) {
			$sitemap->addItem('/Server/'.$value['alias'].'/', '0.8', 'weekly', date('Y-m-d'));
		}
		$goods = Db::name('goods')->order('id', 'desc')->select();
		foreach ($goods as $key => $value) {
			$sitemap->addItem('/stand/'.$value['alias'].'/', '0.5', 'monthly', date('Y-m-d'));
		}
		//文章
		$sitemap->addItem('/Solution/', '0.8', 'daily', date('Y-m-d'));
		$infocate = Db::name('category')->where('pid', 'in', '2,3,4,18,8,9,10,11,12,13,177')->order('id', 'desc')->select();
		foreach ($infocate as $key => $value) {
			$sitemap->addItem('/Solution/'.$value['alias'].'/', '0.8', 'weekly', date('Y-m-d'));
		}
		$article = Db::name('article')->order('id', 'desc')->select();
		foreach ($article as $key => $value) {
			$sitemap->addItem('/article/'.$value['alias'].'/', '0.5', 'monthly', date('Y-m-d'));
		}
		//购买说明
		$sitemap->addItem('/Introduce', '0.5', 'never', date('Y-m-d'));
		//联系我们
		$sitemap->addItem('/Contact', '0.5', 'never', date('Y-m-d'));
		//标签
		$tags = Db::name('tags')->order('id', 'desc')->select();
		foreach ($tags as $key => $value) {
			$sitemap->addItem('/tag/'.$value['name'], '0.8', 'daily', date('Y-m-d'));
		}
		 
		$sitemap->endSitemap();
		echo 'ok';
	}
}