<?php
/**
 * 解决方案
 */
namespace app\spider\controller;
use app\spider\SpiderBase;
use app\common\model\Article;
use app\common\model\Category;

class Solution extends SpiderBase
{
	/**
	 * 文章列表
	 */
	public function list()
	{
		$data = input('param.');
        $page = isset($data['page'])?$data['page']:1;
        $pageSize = 8;

        $list = Article::order('id', 'desc')->paginate([
            'list_rows' => $pageSize,
            'page' => $page,
            'var_page' => 'page'
        ]);

        $datalist = [];
        foreach ($list as $key => $value) {
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $value["content"], $match);
            $list[$key]['headimg'] = isset($match[1][0])?$match[1][0]:"https://www.kaihu123.com/static/aliyun/images/1.png";
            $cate = Category::find($value['cid']);

            $datalist[$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'intro' => mb_substr($value['intro'], 0, 25),
                'headimg' => isset($match[1][0])?$match[1][0]:"https://www.kaihu123.com/static/aliyun/images/1.png",
                'classname' => $cate['name'],
                'cid' => $value['cid'],
                'addtime' => date('m-d H:i:s', $value['addtime'])
            ];
        }

        return $this->result(['data' => $datalist], 200, 'OK');
	}

	/**
	 * 最新文章
	 */
	public function newlist()
	{
		$data = input('param.');
        $list = Article::order('id', 'desc')->limit(8)->select();
        $datalist = [];
        foreach ($list as $key => $value) {
        	$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $value["content"], $match);
            $list[$key]['headimg'] = isset($match[1][0])?$match[1][0]:"https://www.kaihu123.com/static/aliyun/images/1.png";
            $cate = Category::find($value['cid']);

            $datalist[$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'intro' => mb_substr($value['intro'], 0, 25),
                'headimg' => isset($match[1][0])?$match[1][0]:"https://www.kaihu123.com/static/aliyun/images/1.png",
                'classname' => $cate['name'],
                'cid' => $value['cid'],
                'addtime' => date('m-d H:i:s', $value['addtime'])
            ];
        }

        return $this->result($datalist, 200, 'OK');
	}

	/**
     * 文章分类
     */
    public function subclass()
    {
        $data = input('param.');
        $page = isset($data['page'])?$data['page']:1;
        $pageSize = 8;
        //云分类
        $category = Category::where('id', $data['cid'])->find();
        if (empty($category)) {
            return $this->result('', 500, '分类不存在！');
        }
        //父分类
        $fcate = Category::find($category['pid']);
        $category['parentname'] = $fcate['name'];
        // 云产品
        $list = Article::where('cid', $data['cid'])->order('id', 'desc')->paginate([
            'list_rows' => $pageSize,
            'page' => $page,
            'var_page' => 'page'
        ]);

        $datalist = [];
        foreach ($list as $key => $value) {
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $value["content"], $match);
            $list[$key]['headimg'] = isset($match[1][0])?$match[1][0]:"https://www.kaihu123.com/static/aliyun/images/1.png";
            $cate = Category::find($value['cid']);

            $datalist[$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'intro' => mb_substr($value['intro'], 0, 25),
                'headimg' => isset($match[1][0])?$match[1][0]:"https://www.kaihu123.com/static/aliyun/images/1.png",
                'classname' => $cate['name'],
                'cid' => $value['cid'],
                'addtime' => date('m-d H:i:s', $value['addtime'])
            ];
        }

        $result = [
            'list' => ['data' => $datalist],
            'category' => $category->toArray()
        ];

        return $this->result($result, 200, 'OK');
    }

    /**
     * 文章详细
     */
    public function info()
    {
        $data = input('param.');
        $info = Article::find($data['id']);

        $cate = Category::find($info['cid']);
        $info['classname'] = $cate['name'];
        $info['addtime'] = date('m-d H:i:s', $info['addtime']);

        $datalist = $info->toArray();

        return $this->result($datalist, 200, 'OK');
    }
}