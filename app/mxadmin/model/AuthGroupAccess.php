<?php
/**
 * 分组权限
 * @author tigger
 */
declare (strict_types = 1);

namespace app\mxadmin\model;

use think\Model;

class AuthGroupAccess extends Model
{
    /**
     * @return \think\model\relation\HasOne
     */
    public function group()
    {
        return $this->hasOne(AuthGroup::class,'id','group_id')->bind(['title']);
    }
}