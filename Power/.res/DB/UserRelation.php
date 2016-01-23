<?php

/* *
 * 将以下关系添加进用户模型的关系文件里
 * */

trait UserRelation{

    /*
     * 与Role模型 多对多
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            'App\Models\UserSystem\Role\Role',
            'user_has_role',
            'user_id',
            'role_id'
        );
    }
}
