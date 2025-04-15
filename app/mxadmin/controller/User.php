<?php
/**
 * 用户
 */
namespace app\mxadmin\controller;
use app\mxadmin\AdminBase;
use app\common\model\User as UserModel;

class User extends AdminBase
{
	/**
	 * 展示
	 */
	public function index()
	{
		return view();
	}

	/**
	 * 数据
	 */
	public function datalist($limit = 15)
	{
		$list = UserModel::order('id', 'desc')->paginate($limit);

		foreach ($list as $key => $value) {
			$list[$key]['logintime'] = $value['logintime']?date('Y-m-d H:i:s', $value['logintime']):'未登录';
			$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
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
			$search = new UserModel();
			if ($data['name'] != '') {
				$search = $search->where('username|email', $data['name']);
			}

			$list = $search->order('id', 'desc')->paginate($limit);
			foreach ($list as $key => $value) {
				$list[$key]['logintime'] = $value['logintime']?date('Y-m-d H:i:s', $value['logintime']):'未登录';
				$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
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
				'nickname' => $this->request->param('nickname', '', 'trim'),
				'email' => $this->request->param('email', '', 'trim'),
				'mobile' => $this->request->param('mobile', '', 'trim'),
				'wechat' => $this->request->param('wechat', '', 'trim'),
				'qq' => $this->request->param('qq', '', 'trim'),
				'telegram' => $this->request->param('telegram', '', 'trim'),
				'pass' => $this->request->param('newpass', '', 'trim')
			];

			$data['addtime'] = time();
			$data['pass'] = md5($data['pass']);
			
			$result = UserModel::create($data);
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
				'nickname' => $this->request->param('nickname', '', 'trim'),
				'email' => $this->request->param('email', '', 'trim'),
				'mobile' => $this->request->param('mobile', '', 'trim'),
				'wechat' => $this->request->param('wechat', '', 'trim'),
				'qq' => $this->request->param('qq', '', 'trim'),
				'telegram' => $this->request->param('telegram', '', 'trim'),
				'pass' => $this->request->param('newpass', '', 'trim')
			];

			if (empty($data['pass'])) {
				unset($data['pass']);
			}else{
				$data['pass'] = md5($data['pass']);
			}

			$result = UserModel::update($data);
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
            $result = UserModel::update(['status' => $data['status']], ['id' => $id]);

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
            $result = UserModel::destroy($ids);
            if ($result == true) {
                return $this->success('删除成功');
            } else {
                return $this->error('删除失败');
            }
        }
    }
}