<?php
/**
 * 角色管理
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\controller;

use app\mxadmin\AdminBase;
use app\mxadmin\model\AuthRule;
use app\mxadmin\model\AuthGroup;
use app\mxadmin\model\AuthGroupAccess;
use think\exception\ValidateException;

class Role extends AdminBase
{
    /**
     * 角色管理
     * @return \think\response\View
     */
    public function index()
    {
        return view();
    }

    /**
     * 返回Json格式的数据
     * @param int $limit
     * @throws \think\db\exception\DbException
     */
    public function datalist($limit=15)
    {
        $list = AuthGroup::order('id')->paginate($limit);
        return $this->result($list);
    }

    /**
     * 添加角色
     */
    public function add()
    {
        if (request()->isPost()) {
            //$data = input('param.');
            $data = [
                'id' => $this->request->param('id', '', 'trim'),
                'title' => $this->request->param('title', '', 'trim'),
                'comments' => $this->request->param('comments', '', 'trim'),
            ];
            try {
                $this->validate($data, 'Role');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            $result = AuthGroup::create($data);
            if ($result == true) {
                return $this->success('角色添加成功');
            } else {
                return $this->error('角色添加失败');
            }
        }
    }

    /**
     * 修改角色
     * @param $id
     */
    public function edit($id)
    {
        if (request()->isPost()) {
            //$data = input('param.');
            $data = [
                'id' => $this->request->param('id', '', 'trim'),
                'title' => $this->request->param('title', '', 'trim'),
                'comments' => $this->request->param('comments', '', 'trim'),
            ];
            try {
                $this->validate($data, 'Role');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            $result = AuthGroup::update($data, ['id' => $id]);
            if ($result == true) {
                return $this->success('角色修改成功');
            } else {
                return $this->error('角色修改失败');
            }
        }
    }

    /**
     * 删除角色
     * @param $id
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
            $result = AuthGroup::destroy($ids);
            if ($result == true) {
                // 删除角色组
                AuthGroupAccess::whereIn('group_id', $ids)->delete();
                return $this->success('角色删除成功');
            } else {
                return $this->error('角色删除失败');
            }
        }
    }

    /**
     * 权限分配
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function authlist($id)
    {
        if (request()->isPost()) {
            $data = AuthGroup::where('id',$id)->find();
            $list = AuthRule::field('id,pid,title')->order(['weight', 'id'])->select();
            if(!(is_null($data['rules']))){
                foreach ($list as $k => $v) {
                    $list[$k]['checked'] = in_array($v['id'], explode(',', $data['rules']));
                }
            }
            return $this->result($list, 1);
        }
    }

    /**
     * 修改权限分配
     * @param $id
     */
    public function authlist_edit_same($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            $result = AuthGroup::update($data, ['id' => $id]);

            if ($result == true) {
                return $this->success('权限分配成功');
            } else {
                return $this->error('权限分配失败');
            }
        }
    }
}
