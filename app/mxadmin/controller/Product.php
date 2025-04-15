<?php
/**
 * 商品管理
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Category;
use app\common\model\Goods;
use app\common\model\Tags as TagsModel;
use app\common\model\PageMap;

use app\common\controller\Original as OriginalModel;
use app\common\controller\Article as ArtModel;
use think\exception\ValidateException;
class Product extends AdminBase
{
	/**
	 * 展示
	 */
	public function index()
	{
		$data = (new Category())->getSubClass(59);
		$class = list_to_tree($data, true);
		return view('', [
			'class' => $class
		]);
	}

	/**
	 * 数据
	 */
	public function datalist($limit = 15)
	{
		$list = Goods::order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$cate = (new Category())->getClass($value['cid']);
			$list[$key]['classname'] = $cate['name'];
			$list[$key]['configinfo'] = 'cpu:'.$value['cpu'].'  内存:'.$value['ram'].'<br>硬盘:'.$value['hdd'].' 带宽:'.$value['bandwidth'].' ip数:'.$value['ips'];
			$selling = $value['selled']*$value['discount'];
			$list[$key]['price'] = '￥'.number_format($selling, 2);
		}
		return $this->result($list);
	}

	/**
	 * 搜索
	 */
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new Goods();
			if ($data['title'] != '') {
				$search = $search->where('title', 'like', '%'.$data['title'].'%');
			}

			if ($data['cid'] > 0) {
				$search = $search->where('cid', $data['cid']);
			}

			$list = $search->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$cate = (new Category())->getClass($value['cid']);
				$list[$key]['classname'] = $cate['name'];
				$list[$key]['configinfo'] = 'cpu:'.$value['cpu'].'  内存:'.$value['ram'].'<br>硬盘:'.$value['hdd'].' 带宽:'.$value['bandwidth'].'ip数:'.$value['ips'];
				$selling = $value['selled']*$value['discount'];
				$list[$key]['price'] = number_format($selling, 2);
			}
			return $this->result($list);
		}
	}

	//弹出
	public function form()
	{
		$data = (new Category())->getSubClass(59);
		$category = list_to_tree($data, true);
		return view('', [
			'category' => $category
		]);
	}

	//添加
	public function add()
	{
		if (request()->isPost()) {
			$data = [
				'title' => $this->request->param('title', '', 'trim'),
				'alias' => $this->request->param('alias', '', 'trim'),
				'cid' => $this->request->param('cid', '', 'trim'),
				'description' => $this->request->param('description', '', 'trim'),
				'cpu' => $this->request->param('cpu', '', 'trim'),
				'ram' => $this->request->param('ram', '', 'trim'),
				'hdd' => $this->request->param('hdd', '', 'trim'),
				'bandwidth' => $this->request->param('bandwidth', '', 'trim'),
				'ips' => $this->request->param('ips', '', 'trim'),
				'selled' => $this->request->param('selled', '', 'trim'),
				'discount' => $this->request->param('discount', '', 'trim'),
				'intro' => $this->request->param('intro', '', 'trim'),
				'num' => $this->request->param('num', '', 'trim')
			];

			$data['addtime'] = time();
			$alias = delPunctuation($data['alias']);
			$alias = strtolower($alias);
            $data['alias'] = str_replace(' ', '-', $alias);
			$isone = Goods::where('alias', $data['alias'])->find();
			if (!empty($isone)) {
				return $this->error('英文别名已存在');
			}
			//数据添加
			$result = Goods::create($data);
			if ($result == true) {
				//提交百度
				//postBaiduPage('/stand/'.$data['alias'].'.html');
				$this->get_tags($result->id, $data['title'], $data['intro']);
				//关键词获取
				$this->get_keywords($result->id, $data['intro']);
				return $this->success('添加成功！');
			}else{
				return $this->error('添加失败');
			}
		}
	}

	//修改
	public function edit($id)
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'title' => $this->request->param('title', '', 'trim'),
				'alias' => $this->request->param('alias', '', 'trim'),
				'cid' => $this->request->param('cid', '', 'trim'),
				'description' => $this->request->param('description', '', 'trim'),
				'cpu' => $this->request->param('cpu', '', 'trim'),
				'ram' => $this->request->param('ram', '', 'trim'),
				'hdd' => $this->request->param('hdd', '', 'trim'),
				'bandwidth' => $this->request->param('bandwidth', '', 'trim'),
				'ips' => $this->request->param('ips', '', 'trim'),
				'selled' => $this->request->param('selled', '', 'trim'),
				'discount' => $this->request->param('discount', '', 'trim'),
				'intro' => $this->request->param('intro', '', 'trim'),
				'num' => $this->request->param('num', '', 'trim')
			];

			$alias = delPunctuation($data['alias']);
			$alias = strtolower($alias);
            $data['alias'] = str_replace(' ', '-', $alias);
			$info = Goods::find($data['id']);
			if ($info['alias'] != $data['alias']) {
				$isone = Goods::where('alias', $data['alias'])->find();
				if (!empty($isone)) {
					return $this->error('英文别名已存在');
				}
			}
			
			$result = Goods::update($data);
			if ($result == true) {
				return $this->success('修改成功');
			}else{
				return $this->error('修改失败');
			}
		}
	}

	/**
	 * 获取文章关键词
	 * @param source_id 文章ID
	 * @param content 文章内容
	 */
	public function get_keywords($source_id = 0, $content = '')
	{
		$art = new ArtModel();
		$notag = strip_tags($content);
		$allkey = $art->getKeywords($notag);
		$time = time();
		if (isset($allkey['results'])) {
			foreach ($allkey['results'] as $key => $value) {
				$tag_id = 0;
				//入库关键词检查
				$judge_tag = TagsModel::where('name', $value['word'])->find();
				if (empty($judge_tag)) {
					$insert_tag = [
						'name' => $value['word'],
						'goods_id' => $source_id,
						'is_goods' => 1,
						'addtime' => $time
					];
					$tag = TagsModel::create($insert_tag);
					$tag_id = $tag->id;
				}else{
					$tag_id = $judge_tag['id'];
					//获取链接地址
					$link = getDomaindata($judge_tag);
					//替换内容链接，并进行更新内容
					$new_content = str_replace($value['word'], $link, $content);
					$article_update = [
						'id' => $source_id,
						'intro' => $new_content
					];

					$goods = Goods::update($article_update);
				}

				//入库标签管理 page_map
				$insert_page_map = [
					'tag_id' => $tag_id,
					'page_id' => $source_id,
					'is_article' => 1,
					'addtime' => $time
				];

				$pagemap = PageMap::create($insert_page_map);
			}
		}

		return true;
	}

	/**
	 * 获取内容标签
	 * @param title 标题
	 * @param content 内容
	 */
	public function get_tags($source_id = 0, $title = '', $content = '')
	{
		$art = new ArtModel();
		$alltags = $art->getTags($title, $content);
		$time = time();
		if (isset($alltags['items'])) {
			foreach ($alltags['items'] as $key => $value) {
				$tag_id = 0;
				//入库标签库 tags
				$judge_tag = TagsModel::where('name', $value['tag'])->find();
				if (empty($judge_tag)) {
					$insert_tag = [
						'name' => $value['tag'],
						'goods_id' => $source_id,
						'is_goods' => 1,
						'addtime' => $time
					];
					$tag = TagsModel::create($insert_tag);
					$tag_id = $tag->id;
				}else{
					$tag_id = $judge_tag['id'];
					//获取链接地址
					$link = getDomaindata($judge_tag);
					//替换内容链接，并进行更新内容
					$new_content = str_replace($value['tag'], $link, $content);
					$article_update = [
						'id' => $source_id,
						'intro' => $new_content
					];

					$goods = Goods::update($article_update);
				}

				//入库标签管理 page_map
				$insert_page_map = [
					'tag_id' => $tag_id,
					'page_id' => $source_id,
					'is_article' => 1,//服务器
					'addtime' => $time
				];

				$pagemap = PageMap::create($insert_page_map);
			}

			return true;
		}
	}

	//删除
	public function del($id)
	{
		if (request()->isPost()) {
            $data = input('param.');
            if (empty($id)) {
                $ids = explode(',', $data['ids']);
            } else {
                $ids = $id;
            }
            $result = Goods::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
	}

	/**
     * 修改状态
     */
    public function edit_state_same($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            $result = Goods::update(['status' => $data['status']], ['id' => $id]);

            if ($result == true) {
                return $this->success($data['status'] ? '已推荐' : '已取消');
            } else {
                return $this->error('状态修改失败');
            }
        }
    }
}