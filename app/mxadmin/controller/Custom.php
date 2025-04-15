<?php
/**
 * 客服管理
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Custom as CustomModel;

class Custom extends AdminBase
{
	//展示
	public function index()
	{
		return view();
	}

	//数据
	public function datalist($limit = 15)
	{
		$list = CustomModel::order('id', 'desc')->paginate($limit);

		return $this->result($list);
	}

	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new CustomModel();

			if ($data['type'] != '') {
				$search = $search->where('type', $data['type'] - 1);
			}

			if ($data['status'] != '') {
				$search = $search->where('status', $data['status'] - 1);
			}

			$list = $search->order('id', 'desc')->paginate($limit);

			return $this->result($list);
		}
	}

	//添加
	public function add()
	{
		if (request()->isPost()) {
			$data = [
				'type' => $this->request->param('type', '', 'trim'),
				'nickname' => $this->request->param('nickname', '', 'trim'),
				'code' => $this->request->param('code', '', 'trim'),
				'url' => $this->request->param('url', '', 'trim')
			];

			$data['addtime'] = time();
			$result = CustomModel::create($data);
			if ($result == true) {
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
				'id' =>$this->request->param('id', '', 'trim'),
				'type' => $this->request->param('type', '', 'trim'),
				'nickname' => $this->request->param('nickname', '', 'trim'),
				'code' => $this->request->param('code', '', 'trim'),
				'url' => $this->request->param('url', '', 'trim')
			];

			$result = CustomModel::update($data);
			if ($result == true) {
				return $this->success('修改成功！');
			}else{
				return $this->error('修改失败');
			}
		}
	}

	//修改状态
	public function edit_state_same($id = 0)
	{
		if (request()->isPost()) {
            $data = input('param.');
            
        	$result = UsdtAddr::update(['status' => $data['status']], ['id' => $id]);
            if ($result == true) {
                return $this->success($data['status'] ? '已启用' : '已禁用');
            } else {
                return $this->error('状态修改失败');
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
            $result = CustomModel::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
	}
}