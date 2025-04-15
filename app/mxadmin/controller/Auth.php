<?php
// +----------------------------------------------------------------------
// | mxAdmin
// +----------------------------------------------------------------------
// | 版权所有 2020~2050 福州目雪科技有限公司 [ http://www.muxue.com.cn ]
// +----------------------------------------------------------------------
// | 演示地址: http://demo.muxue.com.cn
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/muxue2020/mxAdmin
// +----------------------------------------------------------------------
// | Author: 明仔 <350656405@qq.com>    微信号：zlmlovem
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\mxadmin\controller;

use app\mxadmin\AdminBase;
use app\mxadmin\model\AuthRule;
use think\exception\ValidateException;

class Auth extends AdminBase
{
    /**
     * 无需权限判断的方法
     * @var array
     */
    protected $noNeedAuth = ['icon'];

    /**
     * 规则管理
     * @return \think\response\View
     */
    public function index()
    {
        return view();
    }

    /**
     * 返回Json格式的数据
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function datalist()
    {
        $list = AuthRule::order(['weight','id'])->select();
        return $this->result($list);
    }

    /**
     * 展开/折叠规则
     */
    public function datalist_open_same()
    {
        if (request()->isPost()) {
            $openType = input('post.type/d');
            if ($openType == 0) {
                $data = AuthRule::select();
                foreach($data as $k => $v){
                    $children = AuthRule::where('pid', $v['id'])->count();
                    if($children == 0 || $v['pid'] == 0){
                        AuthRule::update(['open' => 1], ['id' => $v['id']]);
                    }else{
                        AuthRule::update(['open' => 0], ['id' => $v['id']]);
                    }
                }
            } else if($openType == 1) {
                AuthRule::update(['open' => 1], ['open' => 0]);
            }
            $list = AuthRule::order(['weight','id'])->select();
            return $this->result($list);
        }
    }

    /**
     * 添加规则
     */
    public function add()
    {
        if (request()->isPost()) {
            //$data = input('param.');
            $data = [
                'id' => $this->request->param('id', '', 'trim'),
                'icon' => $this->request->param('icon', '', 'trim'),
                'ismenu' => $this->request->param('ismenu', '', 'trim'),
                'isnav' => $this->request->param('isnav', '', 'trim'),
                'name' => $this->request->param('name', '', 'trim'),
                'pid' => $this->request->param('pid', '', 'trim'),
                'select' => $this->request->param('select', '', 'trim'),
                'title' => $this->request->param('title', '', 'trim'),
                'weight' => $this->request->param('weight', '', 'trim'),
            ];
            try {
                $this->validate($data, 'Auth');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            $result = AuthRule::create($data);
            if ($result == true) {
                return $this->success('规则添加成功');
            } else {
                return $this->error('规则添加失败');
            }
        }
    }

    /**
     * 修改规则
     */
    public function edit($id)
    {
        if (request()->isPost()) {
            //$data = input('param.');
            $data = [
                'id' => $this->request->param('id', '', 'trim'),
                'icon' => $this->request->param('icon', '', 'trim'),
                'ismenu' => $this->request->param('ismenu', '', 'trim'),
                'isnav' => $this->request->param('isnav', '', 'trim'),
                'name' => $this->request->param('name', '', 'trim'),
                'pid' => $this->request->param('pid', '', 'trim'),
                'select' => $this->request->param('select', '', 'trim'),
                'title' => $this->request->param('title', '', 'trim'),
                'weight' => $this->request->param('weight', '', 'trim'),
            ];
            try {
                $this->validate($data, 'Auth');
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            $result = AuthRule::update($data, ['id' => $id]);
            if ($result == true) {
                return $this->success('规则修改成功');
            } else {
                return $this->error('规则修改失败');
            }
        }
    }

    /**
     * 修改排序
     */
    public function edit_weight_same($id)
    {
        if (request()->isPost()) {
            $data = input('param.');
            $result = AuthRule::update(['weight' => $data['weight']], ['id' => $id]);
            if ($result == true) {
                return $this->success('修改成功');
            } else {
                return $this->error('修改失败');
            }
        }
    }

    /**
     * 删除规则
     */
    public function del($id)
    {
        if (request()->isPost()) {
            $result = AuthRule::destroy($id);
            if ($result == true) {
                return $this->success('规则删除成功');
            } else {
                return $this->error('规则删除失败');
            }
        }
    }

    /**
     * 图标选择页面
     * @return \think\response\View
     */
    public function icon()
    {
        return view();
    }
}
