<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $roles = [
            ['name' => 'Admin','guard_name'=>'sanctum'],
            ['name' => 'Management','guard_name'=>'sanctum'],
            ['name' => 'Operation','guard_name'=>'sanctum'],
        ];
        Role::insert($roles);

        $user=\App\Models\User::create(
            [
                'name'=>'Jamal Apriadi',
                'email'=>'jamal.apriadi@gmail.com',
                'email_verified_at'=>date('Y-m-d H:i:s'),
                'password'=>bcrypt('welcome'.date('Y')),
                'profile_picture'=>'default_avatar.png',
                'active'=>'Y',
                // 'role_id'=>$role->id
            ]
        );

        $role1= Role::where('name','Admin')->first();
        $user->assignRole($role1);
    }
}
