<?php
/**
 * 配置管理
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\controller;

use app\mxadmin\AdminBase;
use app\mxadmin\model\Config;
use think\exception\ValidateException;

class Configure extends AdminBase
{
    /**
     * 消息
     * @return \think\response\View
     */
    public function index()
    {
        return view('', [
            'sysconf' => Config::getConfigData('system'),
            'storage' => Config::getConfigData('storage'),
            'setconfig' => Config::getConfigData('setconfig'),
            'setcontact' => Config::getConfigData('setcontact'),
        ]);
    }

    /**
     * 保存配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function submit()
    {
        if (request()->isPost()) {
            $data = input('param.');
            try {
                $this->validate($data, 'Configure.'.$data['typename']);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return $this->error($e->getError());
            }
            Config::setConfigData($data['typename'], $data);
            return $this->success('保存成功');
        }
    }
}
