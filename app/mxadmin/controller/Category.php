<?php
/**
 * 技术分类
 */
declare (strict_types = 1);
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Category as CategoryModel;
use think\exception\ValidateException;

class Category extends AdminBase
{
	protected $cate_model;

	public function initialize()
	{
		$this->cate_model = new CategoryModel();
	}

	//展示
	public function index()
	{
		return view();
	}

	//数据展示
	public function datalist()
	{
		$list = $this->cate_model->order(['sort', 'id'])->select();
		return $this->result($list);
	}

	//修改排序
	public function edit_weight_same($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            $result = $this->cate_model->update(['sort' => $data['weight']], ['id' => $id]);
            if ($result == true) {
                return $this->success('修改成功');
            } else {
                return $this->error('修改失败');
            }
        }
    }

    /**
     * 展开/折叠规则
     */
    public function datalist_open_same()
    {
        if (request()->isPost()) {
            $openType = input('post.type/d');
            if ($openType == 0) {
                $data = $this->cate_model->select();
                foreach($data as $k => $v){
                    $children = $this->cate_model->where('pid', $v['id'])->count();
                    if($children == 0 || $v['pid'] == 0){
                        $this->cate_model->update(['open' => 1], ['id' => $v['id']]);
                    }else{
                        $this->cate_model->update(['open' => 0], ['id' => $v['id']]);
                    }
                }
            } else if($openType == 1) {
                $this->cate_model->update(['open' => 1], ['open' => 0]);
            }
            $list = $this->cate_model->order(['sort','id'])->select();
            return $this->result($list);
        }
    }

    //添加
    public function add()
    {
        if (request()->isPost()) {
            //$data = input('param.');
            $data = [
                'id' => $this->request->param('id', '', 'trim'),
                'name' => $this->request->param('name', '', 'trim'),
                'alias' => $this->request->param('alias', '', 'trim'),
                'hot' => $this->request->param('hot', '', 'trim'),
                'pid' => $this->request->param('pid', '', 'trim'),
                'select' => $this->request->param('select', '', 'trim'),
                'title' => $this->request->param('title', '', 'trim'),
                'intro' => $this->request->param('intro', '', 'trim'),
                'sort' => $this->request->param('sort', '', 'trim'),
            ];

            try {
                $this->validate($data, 'Cate');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }

            $data['alias'] = str_replace(' ', '-', $data['alias']);
            $data['alias'] = strtolower($data['alias']);
            //判断是否唯一
            $isone = $this->cate_model->where('alias', $data['alias'])->find();
            if (!empty($isone)) {
                return $this->error('英文别名已存在');
            }
            $result = $this->cate_model->create($data);
            if ($result == true) {
                return $this->success('添加成功');
            } else {
                return $this->error('添加失败');
            }
        }
    }

    //修改
    public function edit($id)
    {
        if (request()->isPost()) {
            //$data = input('param.');
            $data = [
                'id' => $this->request->param('id', '', 'trim'),
                'name' => $this->request->param('name', '', 'trim'),
                'alias' => $this->request->param('alias', '', 'trim'),
                'hot' => $this->request->param('hot', '', 'trim'),
                'pid' => $this->request->param('pid', '', 'trim'),
                'select' => $this->request->param('select', '', 'trim'),
                'title' => $this->request->param('title', '', 'trim'),
                'description' => $this->request->param('description', '', 'trim'),
                'intro' => $this->request->param('intro', '', 'trim'),
                'template' => $this->request->param('template', '', 'trim'),
                'template_name' => $this->request->param('template_name', '', 'trim'),
                'sort' => $this->request->param('sort', '', 'trim'),
            ];
            try {
                $this->validate($data, 'Cate');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }

            $data['alias'] = str_replace(' ', '-', $data['alias']);
            $data['alias'] = strtolower($data['alias']);
            //判断数据
            $info = $this->cate_model->find($data['id']);
            if ($info['alias'] != $data['alias']) {
                $isone = $this->cate_model->where('alias', $data['alias'])->find();
                if (!empty($isone)) {
                    return $this->error('英文别名已存在');
                }
            }
            
            $result = $this->cate_model->update($data, ['id' => $id]);
            if ($result == true) {
                return $this->success('修改成功');
            } else {
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
            $result = $this->cate_model->destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
    }
}