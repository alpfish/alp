<?php

namespace Alp\Power\Contracts;

interface Power
{
    /**
     * 判定当前用户是否拥有指定的角色
     *
     * @param  string|array $roles
     * @param  bool $all
     * @return bool
     */
    public function hasRole($roles, $all = false);

    /*
     * 判定当前用户是否拥有指定的权限
     *
     * @param  string|array $permissions
     * @param  bool $all
     * @return bool
     */
    public function hasPermission($permissions, $all = false);

    /*
     * hasPermission()的别名
     */
    public function may($permission, $all = false);
}