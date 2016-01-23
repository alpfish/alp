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

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('show_name');
            $table->string('description')->nullable();
            $table->integer('parent_id')->nullable();
            $table->tinyInteger('sort')->nullable();
            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropUnique('permissions_name_unique');
        });
        Schema::drop('permissions');
    }
}
