<?php

namespace App\Models\UserSystem\Role;

trait RoleRelation
{
    /*
     * 与User模型 多对多
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {

        return $this->belongsToMany(
            'App\Models\UserSystem\User\User',
            'user_has_role',
            'role_id',
            'user_id');
    }

    /*
     * 与Permission模型 多对多
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions() {

        return $this->belongsToMany(
            'App\Models\UserSystem\Permission\Permission',
            'role_has_permission',
            'role_id',
            'permission_id');
    }
}