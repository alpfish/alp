<?php

namespace Al\Power\Facades;

use Illuminate\Support\Facades\Facade;

class Power extends Facade
{
    /**
     * 得到服务包的快捷名称（门面）.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'power';
    }
}