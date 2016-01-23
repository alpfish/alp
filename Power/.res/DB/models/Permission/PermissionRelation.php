<?php

namespace App\Models\UserSystem\Permission;

trait PermissionRelation
{
    /*
     * 与Role模型 多对多
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            'App\Models\UserSystem\Role\Role',
            'role_has_permission',
            'permission_id',
            'role_id'
        );
    }
}