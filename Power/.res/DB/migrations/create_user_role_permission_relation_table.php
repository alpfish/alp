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

class CreateUsersystemRelationTable extends Migration
{
    public function up()
    {
        Schema::create('user_has_role', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            /*
             * 设置索引/外键，对应外键字段类型和长度必须一致，外键自建相关Index索引
             * */
            //$table->primary(['user_id', 'role_id']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
        });

        Schema::create('role_has_permission', function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->integer('permission_id')->unsigned();

            //$table->primary(['role_id', 'permission_id']);

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
        });

    }


    public function down()
    {
        Schema::table('user_has_role', function (Blueprint $table) {
            $table->dropForeign('user_has_role_user_id_foreign');
            $table->dropForeign('user_has_role_role_id_foreign');
        });

        Schema::table('role_has_permission', function (Blueprint $table) {
            $table->dropForeign('role_has_permission_role_id_foreign');
            $table->dropForeign('role_has_permission_permission_id_foreign');
        });

        Schema::drop('user_has_role');
        Schema::drop('role_has_permission');
    }
}
