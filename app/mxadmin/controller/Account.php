<?php
/**
 * 用户账户
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\UserAccount;
use app\common\model\User;
use app\common\model\Appeal;
use app\common\model\Order;

class Account extends AdminBase
{
	/**
	 * 展示
	 */
	public function index()
	{
		$type = config('status.accType');
		return view('', [
			'type' => $type
		]);
	}

	/**
	 * 数据
	 */
	public function datalist($limit = 15)
	{
		$type = config('status.accType');
		$list = UserAccount::order('id', 'desc')->paginate($limit);

		foreach ($list as $key => $value) {
			$list[$key]['typename'] = $type[$value['type']];
			$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			$user = User::find($value['userid']);
			$list[$key]['users'] = 'ID/昵称：'.$value['userid'].'/'.$user['nickname'].'<br>'.'邮箱：'.$user['email'];

		}

		return $this->result($list);
	}

	/**
	 * 搜索
	 */
	public function search($limit = 15)
	{
		if (request()->isGet()) {
			$data = input('param.');
			$search = new UserAccount();
			$type = config('status.accType');
			if ($data['userid'] != '') {
				$search = $search->where('userid', $data['userid']);
			}

			if ($data['uid'] != '') {
				$search = $search->where('uid', $data['uid']);
			}

			if ($data['email'] != '') {
				$search = $search->where('email', $data['email']);
			}

			if ($data['remarks'] != '') {
				$search = $search->where('remarks', 'like', '%'.$data['remarks'].'%');
			}

			if ($data['type'] > 0) {
				$search = $search->where('type', $data['type'] - 1);
			}

			$list = $search->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$user = User::find($value['userid']);
				$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
				$user = User::find($value['userid']);
				$list[$key]['users'] = 'ID/昵称：'.$value['userid'].'/'.$user['nickname'].'<br>'.'邮箱：'.$user['email'];
			}

			return $this->result($list);
		}
	}

	/**
	 * 添加
	 */
	public function add()
	{
		if (request()->isPost()) {
			$data = [
				'userid' => $this->request->param('userid', '', 'trim'),
				'uid' => $this->request->param('uid', '', 'trim'),
				'email' => $this->request->param('email', '', 'trim'),
				'type' => $this->request->param('type', '', 'trim'),
				'usd' => $this->request->param('usd', '', 'trim'),
				'info' => $this->request->param('info', '', 'trim'),
				'status' => $this->request->param('status', '', 'trim'),
				'state' => $this->request->param('state', '', 'trim'),
				'remarks' => $this->request->param('remarks', '', 'trim'),
				'recharge' => $this->request->param('recharge', '', 'trim')
			];

			$data['addtime'] = time();
			unset($data['recharge']);
			$result = UserAccount::create($data);
			if ($result == true) {
				return $this->success('添加成功');
			}else{
				return $this->error('添加失败');
			}
		}
	}

	/**
	 * 修改
	 */
	public function edit($id)
	{
		if (request()->isPost()) {
			$data = [
				'id' => $this->request->param('id', '', 'trim'),
				'userid' => $this->request->param('userid', '', 'trim'),
				'uid' => $this->request->param('uid', '', 'trim'),
				'email' => $this->request->param('email', '', 'trim'),
				'type' => $this->request->param('type', '', 'trim'),
				'usd' => $this->request->param('usd', '', 'trim'),
				'info' => $this->request->param('info', '', 'trim'),
				'status' => $this->request->param('status', '', 'trim'),
				'state' => $this->request->param('state', '', 'trim'),
				'remarks' => $this->request->param('remarks', '', 'trim'),
				'recharge' => $this->request->param('recharge', '', 'trim')
			];

			$recharge = $data['recharge'];
			unset($data['recharge']);
			$result = UserAccount::update($data);
			if ($result == true) {
				return $this->success('修改成功');
			}else{
				return $this->error('修改失败');
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
            $result = UserAccount::update(['status' => $data['status']], ['id' => $id]);

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
            $result = UserAccount::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
    }
}