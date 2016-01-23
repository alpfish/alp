<?php

namespace Alp\Power\Traits;


trait UserPowerTrait
{
    // --------------------------------------------------------------------
    //                   用户模型权限特征, 在User模型中use
    // --------------------------------------------------------------------

    /*
     * 判定当前用户是否拥有指定的角色
     *
     * @param string(role_name) | numeric(role_id) | Collection(Role::class)
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role))
            return $this->roles->contains('name', $role);

        if (is_numeric($role))
            return $this->roles->contains('id', $role);

        if (is_object($role))
            return !! $this->roles->intersect($role)->count();

        return false;
    }

    /*
     * 判定当前用户是否拥有指定的权限
     *
     * @param string(permission_name) | array([permission_name]) | Permission::class
     * @param bool
     *
     * @return bool
     */
    public function hasPermission($permission, $all = false)
    {
        if (is_string($permission)) {
            foreach ($this->roles as $role) {
                foreach ($role->permissions as $perm){
                    if ($perm->name == $permission)
                        return true;
                }
            }
        }

        /*
         * 权限名参数为数组时，第二个参数$all指明判断是否拥有给出的所有权限
         */
        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $hasPerm = $this->hasPermission($permName);

                if ( $hasPerm && !$all)
                    return true;

                if ( !$hasPerm && $all)
                    return false;
            }

            return $all;
        }

        if (is_object($permission)) {
            return $this->hasRole($permission->roles);
        }

        return false;
    }

    /*
     *  hasPermission()的别名，
     *
     *  若想使用 can() 验证权限，可在app\Providers\AuthServiceProvider.php中加入以下codes:
     *
     *  use App\Models\UserSystem\Permission\Permission;
     *
     *  public function boot(GateContract $gate)
        {
            $this->registerPolicies($gate);

            $permissions = Permission::with('roles')->get();
            foreach ($permissions as $permission){
                $gate->define($permission->name, function($user) use($permission){
                    return $user->may($permission);
                });
            }

        }

        ps: 此can()方法效率远不如现有may()方法, can()适合少量非循环权限定义
     *
     */
    public function may($permission, $all = false)
    {
        return $this->hasPermission($permission, $all);
    }
}