<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insertGetId([
            'allow_group_id' => 1,
            'user_group_id' => 1,
            'username' => 'admin',
            'password' => 'e10adc3949ba59abbe56e057f20f883e',
            'fullname' => 'Admin',
            'email' => 'phuonglh@hiworld.com.vn',
            'mobile' => '0987654321',
            'first_login' => 2,
            'status' => 1,
            'created_by' => 0,
            'updated_by' => 0,
        ]);
    }
}
