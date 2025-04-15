<?php
/**
 * 个人信息
 */
namespace app\member\controller;
use app\member\MemberBase;
use app\common\model\User as UserModel;
use think\exception\ValidateException;

class User extends MemberBase
{
	/**
	 * 用户设置
	 */
	public function index()
	{
		$id = session('user_info.id');
		if (request()->isPost()) {
			$data = [
				'id' => $id,
				'nickname' => $this->request->param('nickname', '', 'trim'),
				'wechat' => $this->request->param('wechat', '', 'trim'),
				'mobile' => $this->request->param('mobile', '', 'trim'),
				'qq' => $this->request->param('qq', '', 'trim'),
				'telegram' => $this->request->param('telegram', '', 'trim'),
				'company' => $this->request->param('company', '', 'trim'),
				'idcode' => $this->request->param('idcode', '', 'trim')
			];

			try {
                $this->validate($data, 'User.Manydata');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }

			$result = UserModel::update($data);
			if ($result == true) {
				return $this->success('修改成功！');
			}else{
				return $this->error('修改失败！');
			}

		}else{
			$user = UserModel::find($id);

			return view('', [
                'user' => $user
            ]);
		}
	}
}