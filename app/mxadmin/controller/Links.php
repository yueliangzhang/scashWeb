<?php
/**
 * 友情链接
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Links as LinksModel;
use think\exception\ValidateException;

class Links extends AdminBase
{
	//展示
	public function index()
	{
		return view();
	}

	//数据
	public function datalist($limit = 15)
	{
		$list = LinksModel::order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$list[$key]['type'] = $value['type'] + 1;
		}
		return $this->result($list);
	}

	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new LinksModel();
			if ($data['name'] != '') {
				$search = $search->where('name', $data['name']);
			}

			$list = $search->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$list[$key]['type'] = $value['type'] + 1;
			}
			return $this->result($list);
		}
	}

	//添加
	public function add()
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'type' => $this->request->param('type', '', 'trim'),
				'name' => $this->request->param('name', '', 'trim'),
				'url' => $this->request->param('url', '', 'trim'),
				'sort' => $this->request->param('sort', '', 'trim')
			];

			if ($data['name'] == "") {
				return $this->error('请输入链接名称');
			}

			if ($data['url'] == "") {
				return $this->error('请输入链接地址');
			}

			$data['type'] = $data['type'] - 1;

			$result = LinksModel::create($data);
			if ($result == true) {
				return $this->success('添加成功！');
			}else{
				return $this->error('添加失败！');
			}
		}
	}

	//修改
	public function edit($id = 0)
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'type' => $this->request->param('type', '', 'trim'),
				'name' => $this->request->param('name', '', 'trim'),
				'url' => $this->request->param('url', '', 'trim'),
				'sort' => $this->request->param('sort', '', 'trim')
			];

			if ($data['name'] == "") {
				return $this->error('请输入链接名称');
			}

			if ($data['url'] == "") {
				return $this->error('请输入链接地址');
			}

			$data['type'] = $data['type'] - 1;

			$result = LinksModel::update($data);
			if ($result == true) {
				return $this->success('修改成功！');
			}else{
				return $this->error('修改失败！');
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
            $result = LinksModel::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
	}
}