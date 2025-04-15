<?php
/**
 * 独立服务器
 */
namespace app\spider\controller;
use app\spider\SpiderBase;
use app\common\model\Goods;
use app\common\model\Category;

class Stand extends SpiderBase
{
	/**
	 * 服务器列表
	 */
	public function list()
	{
		$data = input('param.');
        $page = isset($data['page'])?$data['page']:1;
        $pageSize = 8;

        $list = Goods::order('id', 'desc')->paginate([
            'list_rows' => $pageSize,
            'page' => $page,
            'var_page' => 'page'
        ]);

        $datalist = [];
        foreach ($list as $key => $value) {
            $cate = Category::find($value['cid']);
            $datalist[$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'classname' => $cate['name'],
                'cid' => $value['cid'],
                'cpu' => $value['cpu'],
                'ram' => $value['ram'],
                'hdd' => $value['hdd'],
                'bandwidth' => $value['bandwidth'],
                'ips' => $value['ips'],
                'selled' => $value['selled']
            ];
        }

        return $this->result(['data' => $datalist], 200, 'OK');
	}

	/**
	 * 服务器推荐
	 */
	public function recommend()
	{
		$data = input('param.');
        $list = Goods::where('status', 1)->order('id', 'asc')->select();

        $datalist = [];
        foreach ($list as $key => $value) {
            $cate = Category::find($value['cid']);
            $datalist[$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'classname' => $cate['name'],
                'cid' => $value['cid'],
                'cpu' => $value['cpu'],
                'ram' => $value['ram'],
                'hdd' => $value['hdd'],
                'bandwidth' => $value['bandwidth'],
                'ips' => $value['ips'],
                'selled' => $value['selled']
            ];
        }

        return $this->result($datalist, 200, 'OK');
	}

	/**
     * 服务器分类
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
        $list = Goods::where('cid', $data['cid'])->order('id', 'desc')->paginate([
            'list_rows' => $pageSize,
            'page' => $page,
            'var_page' => 'page'
        ]);

        $datalist = [];
        foreach ($list as $key => $value) {
            $datalist[$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'classname' => $category['name'],
                'cid' => $value['cid'],
                'cpu' => $value['cpu'],
                'ram' => $value['ram'],
                'hdd' => $value['hdd'],
                'bandwidth' => $value['bandwidth'],
                'ips' => $value['ips'],
                'selled' => $value['selled']
            ];
        }

        $datalist = [
            'list' => ['data' => $datalist],
            'category' => $category->toArray()
        ];

        return $this->result($datalist, 200, 'OK');
    }

    /**
     * 服务器详细
     */
    public function standinfo()
    {
        $data = input('param.');
        $info = Goods::find($data['id']);

        $cate = Category::find($info['cid']);
        $info['classname'] = $cate['name'];
        $fcate = Category::find($cate['pid']);
        $info['parentname'] = $fcate['name'];

        $datalist = $info->toArray();

        return $this->result($datalist, 200, 'OK');
    }
}