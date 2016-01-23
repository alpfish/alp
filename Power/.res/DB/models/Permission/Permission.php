<?php

namespace App\Models\UserSystem\Permission;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use PermissionRelation;

    /*
     * 指定Permission模型关联到 permissions 表
     *
     * @var string
     * */
    protected $table = 'permissions';

    /*
     * 指定id不能被批量赋值
     *
     * @var array
     * */
    protected $guarded = ['id'];
}
