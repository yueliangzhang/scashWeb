<?php
/**
 * 公有云
 */
namespace app\spider\controller;
use app\spider\SpiderBase;
use app\common\model\Cloud as CloudModel;
use app\common\model\Category;

class Cloud extends SpiderBase
{
    /**
     * 云产品列表
     */
    public function list()
    {
        $data = input('param.');

        $page = isset($data['page'])?$data['page']:1;
        $pageSize = 8;

        $list = CloudModel::order('id', 'desc')->paginate([
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
                'name' => $value['name'],
                'cid' => $value['cid'],
                'classname' => $cate['name'],
                'alias' => $value['alias'],
                'des' => $value['des'],
                'usd' => $value['usd']
            ];
        }

        return $this->result(['data' => $datalist], 200, 'OK');
    }

    /**
     * 云产品推荐
     */
    public function recommend()
    {
        $data = input('param.');
        $list = CloudModel::where('status', 1)->order('id', 'asc')->select();

        $datalist = [];
        foreach ($list as $key => $value) {
            $cate = Category::find($value['cid']);
            $list[$key]['classname'] = $cate['name'];
            $datalist[$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'name' => $value['name'],
                'cid' => $value['cid'],
                'classname' => $cate['name'],
                'alias' => $value['alias'],
                'des' => mb_substr($value['des'], 0, 50),
                'usd' => $value['usd']
            ];
        }

        return $this->result($datalist, 200, 'OK');
    }

    /**
     * 云分类
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
        $list = CloudModel::where('cid', $data['cid'])->order('id', 'desc')->paginate([
            'list_rows' => $pageSize,
            'page' => $page,
            'var_page' => 'page'
        ]);

        $datalist = [];
        foreach ($list as $key => $value) {
            $datalist[$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'name' => $value['name'],
                'cid' => $value['cid'],
                'classname' => $category['name'],
                'alias' => $value['alias'],
                'des' => $value['des'],
                'usd' => $value['usd']
            ];
        }

        $datalist = [
            'list' => ['data' => $datalist],
            'category' => $category->toArray()
        ];

        return $this->result($datalist, 200, 'OK');
    }

    /**
     * 云详细
     */
    public function cloudinfo()
    {
        $data = input('param.');
        $info = CloudModel::find($data['id']);

        $cate = Category::find($info['cid']);
        $info['classname'] = $cate['name'];
        $fcate = Category::find($cate['pid']);
        $info['parentname'] = $fcate['name'];

        $datalist = $info->toArray();

        return $this->result($datalist, 200, 'OK');
    }
}
