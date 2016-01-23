<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/* *
 * 本迁移只做备份
 * 使用步骤：
 * 1. 创建迁移文件：
 *    php artisan make:migration ...
 *    或
 *    php artisan make:mode -m UserProvider
 *
 * 2. 复制以下结构内容
 *
 * 3. 生成数据表
 *    php artisan migrate
 *
 * */

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); //系统名
            $table->string('show_name'); //显示名
            $table->string('description')->nullable(); //描述
            $table->tinyInteger('sort')->nullable(); //角色显示排序
            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_name_unique');
        });
        Schema::drop('roles');
    }
}
