<?php

namespace App\Models\UserSystem\Role;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use RoleAttribute,
        RoleRelation;

    /*
     * 指定Role模型关联到 roles 表
     *
     * @var string
     * */
    protected $table = 'roles';

    /*
     * 指定id不能被批量赋值
     *
     * @var array
     * */
    protected $guarded = ['id'];
}
