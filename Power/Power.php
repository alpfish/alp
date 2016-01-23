<?php

namespace Alp\Power;

use Alp\Power\Contracts\Power as PowerContract;

class Power implements PowerContract
{
    /*
     * Laravel 应用容器
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /*
     * 获取$app容器
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 获取当前已认证的用户 或 空
     *
     * @return Illuminate\Auth\UserInterface|null
     */
    public function user()
    {
        return $this->app->auth->user();
    }

    /**
     * 判定当前用户是否拥有指定的角色
     *
     * @param  string|array $roles
     * @param  bool $all
     * @return bool
     */
    public function hasRole($roles, $all = false)
    {
        if ($user = $this->user())
            return $user->hasRole($roles, $all);

        return false;
    }

    /*
     * 判定当前用户是否拥有指定的权限
     *
     * @param  string|array $permissions
     * @param  bool $all
     * @return bool
     */
    public function hasPermission($permissions, $all = false)
    {
        if ($user = $this->user())
            return $user->hasPermission($permissions, $all = false);

        return false;
    }

    /*
     * hasPermission()的别名，
     */
    public function may($permission, $all = false)
    {
        return $this->hasPermission($permission, $all);
    }
}