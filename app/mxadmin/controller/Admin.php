<?php
/**
 * 账户管理
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\controller;

use app\mxadmin\AdminBase;
use app\mxadmin\model\AdminModel;
use app\mxadmin\model\AuthGroup;
use app\mxadmin\model\AuthGroupAccess;
use think\exception\ValidateException;

class Admin extends AdminBase
{
    /**
     * 账号管理
     * @return \think\response\View
     */
    public function index()
    {
        $rolelist = AuthGroup::field('id,title')->order('id')->select();
        return view('',[
            'rolelist'  =>  $rolelist,
            'admin_id' => getAdminId(),
        ]);
    }

    /**
     * 返回Json格式的数据
     * @param int $limit
     * @throws \think\db\exception\DbException
     */
    public function datalist($limit=15)
    {
        $list = AdminModel::with(['roles'])->order('id', 'desc')->paginate($limit);
        return $this->result($list);
    }

    /**
     * 搜索数据
     * @param int $limit
     * @throws \think\db\exception\DbException
     */
    public function serach($limit=15)
    {
        if (request()->isGet()) {
            $data = input('param.');
            $serach = new AdminModel();
            if ($data['username'] != '') {
                $serach = $serach->whereLike('username', '%' . $data['username'] . '%');
            }
            if ($data['nickname'] != '') {
                $serach = $serach->whereLike('nickname', '%' . $data['nickname'] . '%');
            }
            if ($data['startDate'] != '' && $data['endDate'] != '') {
                $serach = $serach->whereBetweenTime('create_time', strtotime($data['startDate']), strtotime($data['endDate']) + 86399);
            }
            if ($data['status'] != '') {
                $serach = $serach->where('status', $data['status']);
            }
            $list = $serach->with(['roles'])->order('id', 'desc')->paginate($limit);
            return $this->result($list);
        }
    }

    /**
     * 添加账号
     */
    public function add()
    {
        if (request()->isPost()) {
            //$data = input('param.');
            $data = [
                'id' => $this->request->param('id', '', 'trim'),
                'username' => $this->request->param('username', '', 'trim'),
                'nickname' => $this->request->param('nickname', '', 'trim'),
                'password' => $this->request->param('password', '', 'trim'),
                'newpassword' => $this->request->param('newpassword', '', 'trim'),
                'repassword' => $this->request->param('repassword', '', 'trim'),
                'userEditRoleSel' => $this->request->param('userEditRoleSel', '', 'trim'),
                'role_id' => $this->request->param('role_id', '', 'trim'),
            ];
            try {
                $this->validate($data, 'Admin');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            $data['password'] = $data['newpassword'];
            $result = AdminModel::create($data);

            if ($result == true) {
                // 新增用户所属角色
                $role_id = explode(',',$data['role_id']);
                foreach ($role_id as $value) {
                    $dataset[] = ['uid' => $result->id, 'group_id' => $value];
                }
                AuthGroupAccess::insertAll($dataset);

                return $this->success('账号添加成功');
            } else {
                return $this->error('账号添加失败');
            }
        }
    }

    /**
     * 修改账号
     */
    public function edit($id)
    {
        if (request()->isPost()) {
            //$data = input('param.');
            $data = [
                'id' => $this->request->param('id', '', 'trim'),
                'username' => $this->request->param('username', '', 'trim'),
                'nickname' => $this->request->param('nickname', '', 'trim'),
                'password' => $this->request->param('password', '', 'trim'),
                'newpassword' => $this->request->param('newpassword', '', 'trim'),
                'repassword' => $this->request->param('repassword', '', 'trim'),
                'userEditRoleSel' => $this->request->param('userEditRoleSel', '', 'trim'),
                'role_id' => $this->request->param('role_id', '', 'trim'),
            ];
            try {
                $this->validate($data, 'Admin.edit');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            if (empty($data['newpassword'])) {
                unset($data['password']);
            } else {
                $data['password'] = $data['newpassword'];
            }
            $result = AdminModel::update($data, ['id' => $id]);

            if ($result == true) {
                // 先移除用户所属角色
                AuthGroupAccess::where('uid', $id)->delete();
                // 再新增用户所属角色
                $role_id = explode(',', $data['role_id']);
                foreach ($role_id as $value) {
                    $dataset[] = ['uid' => $id, 'group_id' => $value];
                }
                AuthGroupAccess::insertAll($dataset);

                return $this->success('账号修改成功');
            } else {
                return $this->error('账号修改失败');
            }
        }
    }

    /**
     * 修改账号状态
     */
    public function edit_state_same($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            $value = AdminModel::where('id', $id)->find();
            if ($id == getAdminId()) {
                return $this->error('自已账号禁止禁用');
            }
            if ($value['is_admin'] == 1) {
                return $this->error('超级管理员禁止禁用');
            }
            $result = AdminModel::update(['status' => $data['status']], ['id' => $id]);

            if ($result == true) {
                return $this->success($data['status'] ? '已启用' : '已禁用');
            } else {
                return $this->error('账号状态修改失败');
            }
        }
    }

    /**
     * 删除账号
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
            $idsdata = AdminModel::whereIn('id', $ids)->select();
            foreach($idsdata as $value){
                if ($value['id'] == getAdminId()) {
                    return $this->error('自己账号禁止删除');
                } elseif ($value['is_admin'] == 1) {
                    return $this->error('超级管理员禁止删除');
                }
            }
            $result = AdminModel::destroy($ids);

            if ($result == true) {
                // 删除用户所属角色
                AuthGroupAccess::whereIn('uid', $ids)->delete();

                return $this->success('账号删除成功');
            } else {
                return $this->error('账号删除失败');
            }
        }
    }
}
