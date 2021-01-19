<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_group_id')->nullable()->index();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('mobile', 20)->nullable();
            $table->tinyInteger('first_login')->comment('Đăng nhập lần đầu(1:Đúng,2:Không)');
            $table->tinyInteger('status');
            $table->integer('allow_group_id')->index();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
