<?php
/**
 * 技术分类
 */
declare (strict_types = 1);
namespace app\common\model;
use think\Model;

class Category extends Model
{
    // 获取单个分类
	public function getClass($id = 0)
    {
        $cate = $this->find($id);

        return $cate;
    }

    // 获取所有分类
    public function getAllClass()
    {
        $cate = $this->order('sort', 'asc')->select();

        return $cate->toArray();
    }

    // 获取所有子分类
    public function getSubClass($pid = 0)
    {
        $cate = $this->order('sort', 'asc')->select();

        return getTree($cate->toArray(), $pid);
    }
}