<?php
/**
 * 公有云商品
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;

use app\common\model\Cloud as CloudModel;
use app\common\model\Category;
use app\common\model\Tags as TagsModel;
use app\common\model\PageMap;

use app\common\controller\Original as OriginalModel;
use app\common\controller\Article as ArtModel;
use think\exception\ValidateException;
class Cloud extends AdminBase
{
	//展示
	public function index()
	{
		$data = (new Category())->getSubClass(80);
		$class = list_to_tree($data, true);
		return view('', [
			'class' => $class
		]);
	}

	//数据
	public function datalist($limit = 15)
	{
		$list = CloudModel::order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$cate = (new Category())->getClass($value['cid']);
			$pcate = (new Category())->getClass($cate['pid']);
			$list[$key]['classname'] = $cate['name'];
			$list[$key]['pclass'] = $pcate['name'];
		}
		return $this->result($list);
	}

	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new CloudModel();
			if ($data['title'] != '') {
				$search = $search->where('title', '%'.$data['title'].'%');
			}
			if ($data['cid'] != '') {
				$search = $search->where('cid', $data['cid']);
			}

			$list = $search->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$cate = (new Category())->getClass($value['cid']);
				$pcate = (new Category())->getClass($cate['pid']);
				$list[$key]['classname'] = $cate['name'];
				$list[$key]['pclass'] = $pcate['name'];
			}
			return $this->result($list);
		}
	}

	//弹出
	public function form()
	{
		$data = (new Category())->getSubClass(80);
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
				'cid' => $this->request->param('cid', '', 'trim'),
				'title' => $this->request->param('title', '', 'trim'),
				'name' => $this->request->param('name', '', 'trim'),
				'alias' => $this->request->param('alias', '', 'trim'),
				'des' => $this->request->param('des', '', 'trim'),
				'usd'=> $this->request->param('usd', '', 'trim'),
				'intro' => $this->request->param('intro', '', 'trim')
			];

			$data['addtime'] = time();
			$alias = delPunctuation($data['alias']);
			$alias = strtolower($alias);
            $data['alias'] = str_replace(' ', '-', $alias);
			$isone = CloudModel::where('alias', $data['alias'])->find();
			if (!empty($isone)) {
				return $this->error('英文别名已存在');
			}

			$result = CloudModel::create($data);
			if ($result == true) {
				//提交百度
				//postBaiduPage('/vps/'.$data['alias'].'.html');
				$this->get_tags($result->id, $data['title'], $data['intro']);
				//关键词获取
				$this->get_keywords($result->id, $data['intro']);
				return $this->success('添加成功！');
			}else{
				return $this->error('添加失败');
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
						'is_goods' => 2,
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

					$goods = CloudModel::update($article_update);
				}

				//入库标签管理 page_map
				$insert_page_map = [
					'tag_id' => $tag_id,
					'page_id' => $source_id,
					'is_article' => 2,
					'addtime' => $time
				];

				$pagemap = PageMap::create($insert_page_map);
			}
		}

		return true;
	}

	/**
	 * 获取内容标签
	 * @param source_id 产品ID
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
						'is_goods' => 2,//公有云选择
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

					$goods = CloudModel::update($article_update);
				}

				//入库标签管理 page_map
				$insert_page_map = [
					'tag_id' => $tag_id,
					'page_id' => $source_id,
					'is_article' => 2,//公有云
					'addtime' => $time
				];

				$pagemap = PageMap::create($insert_page_map);
			}

			return true;
		}
	}

	//修改
	public function edit($id)
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'cid' => $this->request->param('cid', '', 'trim'),
				'title' => $this->request->param('title', '', 'trim'),
				'name' => $this->request->param('name', '', 'trim'),
				'alias' => $this->request->param('alias', '', 'trim'),
				'des' => $this->request->param('des', '', 'trim'),
				'usd'=> $this->request->param('usd', '', 'trim'),
				'intro' => $this->request->param('intro', '', 'trim')
			];

			$alias = delPunctuation($data['alias']);
			$alias = strtolower($alias);
            $data['alias'] = str_replace(' ', '-', $alias);
			$info = CloudModel::find($data['id']);
			if ($info['alias'] != $data['alias']) {
				$isone = CloudModel::where('alias', $data['alias'])->find();
				if (!empty($isone)) {
					return $this->error('英文别名已存在');
				}
			}
			
			$result = CloudModel::update($data);
			if ($result == true) {
				return $this->success('修改成功');
			}else{
				return $this->error('修改失败');
			}
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
            $result = CloudModel::destroy($ids);
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
            $result = CloudModel::update(['status' => $data['status']], ['id' => $id]);

            if ($result == true) {
                return $this->success($data['status'] ? '已启用' : '已禁用');
            } else {
                return $this->error('状态修改失败');
            }
        }
    }
}