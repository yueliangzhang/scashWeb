<?php
/**
 * 独立管理
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\Stand as StandModel;
use app\common\model\User;

class Stand extends AdminBase
{
	//展示
	public function index()
	{
		return view();
	}

	//数据
	public function datalist($limit = 15)
	{
		$list = StandModel::order('id', 'desc')->paginate($limit);
		$time = time();
		foreach ($list as $key => $value) {
			$user = User::find($value['uid']);
			$list[$key]['users'] = 'ID/昵称：'.$value['uid'].'/'.$user['nickname'].'<br>'.'邮箱：'.$user['email'];

			// 3天内到期的
			if ($value['endtime'] <= ($time + 3*24*3600)) {
				$endtime = '<font color="red">'.date('Y-m-d', $value['endtime']).'</font>';
			}else if ($value['endtime'] <= ($time + 7*24*3600)) {
				$endtime = '<font color="blue">'.date('Y-m-d', $value['endtime']).'</font>';
			}else{
				$endtime = date('Y-m-d', $value['endtime']);
			}
			$list[$key]['time'] = date('Y-m-d', $value['addtime']).'<br>'.$endtime;
			$list[$key]['endtime'] = date('Y-m-d', $value['endtime']);
		}

		return $this->result($list);
	}


	//搜索
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new StandModel();
			if ($data['ip'] != '') {
				$search = $search->where('content', 'like', '%'.$data['ip'].'%');
			}

			if ($data['uid'] != '') {
				$search = $search->where('uid', $data['uid']);
			}

			if ($data['end_time'] > 0) {
				$search = $search->whereTime('endtime', '<', $data['end_time'].' 23:59:59');
			}

			$time = time();
			$list = $search->order('endtime', 'asc')->paginate($limit);
			foreach ($list as $key => $value) {
				$user = User::find($value['uid']);
				$list[$key]['users'] = 'ID/昵称：'.$value['uid'].'/'.$user['nickname'].'<br>'.'邮箱：'.$user['email'];

				// 3天内到期的
				if ($value['endtime'] <= ($time + 3*24*3600)) {
					$endtime = '<font color="red">'.date('Y-m-d', $value['endtime']).'</font>';
				}else if ($value['endtime'] <= ($time + 7*24*3600)) {
					$endtime = '<font color="blue">'.date('Y-m-d', $value['endtime']).'</font>';
				}else{
					$endtime = date('Y-m-d', $value['endtime']);
				}
				$list[$key]['time'] = date('Y-m-d', $value['addtime']).'<br>'.$endtime;
				$list[$key]['endtime'] = date('Y-m-d', $value['endtime']);
			}

			return $this->result($list);
		}
	}

	//添加
	public function add()
	{
		if (request()->isPost()) {
			$data = [
				'uid' => $this->request->param('uid', '', 'trim'),
				'content' => $this->request->param('content', '', 'trim'),
				'seller' => $this->request->param('seller', '', 'trim'),
				'agsell' => $this->request->param('agsell', '', 'trim'),
				'price' => $this->request->param('price', '', 'trim'),
				'endtime' => $this->request->param('endtime', '', 'trim'),
				'remarks' => $this->request->param('remarks', '', 'trim')
			];

			$data['endtime'] = strtotime($data['endtime']);
			$data['addtime'] = time();
			$result = StandModel::create($data);
			if ($result == true) {
				return $this->success('添加成功！');
			}else{
				return $this->error('添加失败！');
			}
		}
	}

	//修改
	public function edit($id)
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'uid' => $this->request->param('uid', '', 'trim'),
				'content' => $this->request->param('content', '', 'trim'),
				'seller' => $this->request->param('seller', '', 'trim'),
				'agsell' => $this->request->param('agsell', '', 'trim'),
				'price' => $this->request->param('price', '', 'trim'),
				'endtime' => $this->request->param('endtime', '', 'trim'),
				'remarks' => $this->request->param('remarks', '', 'trim')
			];

			$data['endtime'] = strtotime($data['endtime']);
			
			$result = StandModel::update($data);
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
            $result = StandModel::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
	}
}