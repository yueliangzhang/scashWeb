<?php
/**
 * 公告管理
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Notice as NoticeModel;

class Notice extends AdminBase
{
	//展示
	public function index()
	{
		return view();
	}

	//数据
	public function datalist($limit = 15)
	{
		$list = NoticeModel::order('id', 'desc')->paginate($limit);
		foreach ($list as $key => $value) {
			$list[$key]['addtime'] = date('Y-m-d', $value['addtime']);
		}

		return $this->result($list);
	}

	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new NoticeModel();
			if ($data['title'] != '') {
				$search = $search->where('title', '%'.$data['title'].'%');
			}

			$list = $search->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$list[$key]['addtime'] = date('Y-m-d', $value['addtime']);
			}

			return $this->result($list);
		}
	}

	//弹窗
	public function form()
	{
		return view();
	}

	//添加
	public function add()
	{
		if (request()->isPost()) {
			$data = [
				'title' => $this->request->param('title', '', 'trim'),
				'content' => $this->request->param('content', '', 'trim')
			];

			$data['addtime'] = time();
			$result = NoticeModel::create($data);
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
				'id' => $this->request->param('id', '', 'trim'),
				'title' => $this->request->param('title', '', 'trim'),
				'content' => $this->request->param('content', '', 'trim')
			];

			$result = NoticeModel::update($data);
			if ($result == true) {
				return $this->success('修改成功！');
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
            $result = NoticeModel::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
	}
}