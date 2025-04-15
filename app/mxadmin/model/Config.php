<?php
/**
 * 配置模型
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\model;

use think\Model;

class Config extends Model
{
    // 设置json类型字段
    protected $json = ['value'];

    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    /**
     * 设置配置数据
     * @param $type
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function setConfigData($type, $data) {
        $result = self::where('type', $type)->find();
        if ($result == true) {
            self::where('type', $type)->update(['value' => $data]);
            cache($type,null);  // 清空缓存
        } else {
            self::create(['type' => $type, 'value' => $data]);
        }
    }

    /**
     * 获取配置数据
     * @param $type
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getConfigData($type) {
        $result = self::where('type', $type)->find();
        if ($result == true) {
            $data = self::where('type', $type)->cache($type,0,'admin')->find()['value'];
            return $data;
        } else {
            self::create(['type' => $type]);
        }
    }
}