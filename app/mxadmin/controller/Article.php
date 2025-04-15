<?php
/**
 * 文章管理
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Article as ArticleModel;
use app\common\model\Category;
use app\common\model\Tags as TagsModel;
use app\common\model\PageMap;

use app\common\controller\Original as OriginalModel;
use app\common\controller\Article as ArtModel;
use think\exception\ValidateException;

class Article extends AdminBase
{
	/**
	 * 展示
	 */
	public function index()
	{
		$data = (new Category())->getAllClass();
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
		$list = ArticleModel::order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$cate = (new Category())->getClass($value['cid']);
			$pcate = (new Category())->getClass($cate['pid']);
			$list[$key]['classname'] = $cate['name'];
			$list[$key]['pclass'] = $pcate['name'];
			$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
		}

		return $this->result($list);
	}

	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new ArticleModel();

			if ($data['title'] != '') {
				$search = $search->where('title', 'like', '%'.$data['title'].'%');
			}

			if ($data['status'] > 0) {
				$search = $search->where('status', ($data['status'] - 1));
			}

			$list = $search->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$cate = (new Category())->getClass($value['cid']);
				$pcate = (new Category())->getClass($cate['pid']);
				$list[$key]['classname'] = $cate['name'];
				$list[$key]['pclass'] = $pcate['name'];
				$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			}

			return $this->result($list);
		}
	}

	//弹出
	public function form()
	{
		$data = (new Category())->getAllClass();
		$category = list_to_tree($data, true);
		return view('', [
			'category' => $category
		]);
	}

	/**
	 * 添加
	 */
	public function add()
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'title' => $this->request->param('title', '', 'trim'),
				'alias' => $this->request->param('alias', '', 'trim'),
				'cid' => $this->request->param('cid', '', 'trim'),
				'intro' => $this->request->param('intro', '', 'trim'),
				'content' => $this->request->param('content', '', 'trim'),
				'clicks' => $this->request->param('clicks', '', 'trim')
			];

			try {
                $this->validate($data, 'Article');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }

            $alias = delPunctuation($data['alias']);
            $alias = strtolower($alias);
            $data['alias'] = str_replace(' ', '-', $alias);
            $isone = ArticleModel::where('alias', $data['alias'])->find();
			if (!empty($isone)) {
				return $this->error('英文别名已存在');
			}

            $data['addtime'] = time();
            //数据添加
            $result = ArticleModel::create($data);
			if ($result == true) {
				$url = '/article/'.$data['alias'].'.html';
				//提交百度
				//postBaiduPage($url);
				//提交IndexNow
				submitIndexNow($url);
				//获取内容标签
            	$this->get_tags($result->id, $data['title'], $data['content']);
            	//关键词获取
				$this->get_keywords($result->id, $data['content']);
				return $this->success('添加成功！');
			}else{
				return $this->error('添加失败');
			}
		}
	}

	/**
	 * 伪原创生成
	 */
	public function orginal()
	{
		$data = input('param.');
		$content = "";
		$orginal = new OriginalModel($data['content']);
		$be_orginal = $orginal->Content();
		if (empty($be_orginal)) {
			$content = $data['content'];
		}else{
			$content = $be_orginal;
		}

		return $this->result($content);
	}

	/**
	 * 修改
	 */
	public function edit($id)
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'title' => $this->request->param('title', '', 'trim'),
				'alias' => $this->request->param('alias', '', 'trim'),
				'cid' => $this->request->param('cid', '', 'trim'),
				'intro' => $this->request->param('intro', '', 'trim'),
				'content' => $this->request->param('content', '', 'trim'),
				'clicks' => $this->request->param('clicks', '', 'trim')
			];

			try {
                $this->validate($data, 'Article');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }

            $alias = delPunctuation($data['alias']);
            $alias = strtolower($alias);
            $data['alias'] = str_replace(' ', '-', $alias);
            $info = ArticleModel::find($data['id']);
            if ($info['alias'] != $data['alias']) {
            	$isone = ArticleModel::where('alias', $data['alias'])->find();
				if (!empty($isone)) {
					return $this->error('英文别名已存在');
				}
            }

            $result = ArticleModel::update($data);
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
		//去掉标签生成关键词
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
						'goods_id' => 0,
						'is_goods' => 0,
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
						'content' => $new_content
					];

					$article = ArticleModel::update($article_update);
				}

				//入库标签管理 page_map
				$insert_page_map = [
					'tag_id' => $tag_id,
					'page_id' => $source_id,
					'is_article' => 0,
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
						'goods_id' => 0,
						'is_goods' => 0,
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
						'content' => $new_content
					];

					$article = ArticleModel::update($article_update);
				}

				//入库标签管理 page_map
				$insert_page_map = [
					'tag_id' => $tag_id,
					'page_id' => $source_id,
					'is_article' => 0,
					'addtime' => $time
				];

				$pagemap = PageMap::create($insert_page_map);
			}

			return true;
		}
	}

	/**
     * 修改状态
     */
    public function edit_state_same($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            $result = ArticleModel::update(['status' => $data['status']], ['id' => $id]);

            if ($result == true) {
                return $this->success($data['status'] ? '已启用' : '已禁用');
            } else {
                return $this->error('状态修改失败');
            }
        }
    }

	/**
	 * 删除
	 */
	public function del($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            if (empty($id)) {
                $ids = explode(',', $data['ids']);
            } else {
                $ids = $id;
            }
            $result = ArticleModel::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
    }



}