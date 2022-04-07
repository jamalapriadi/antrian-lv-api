<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
        $satu=Permission::create(['name'=>'Show Home','guard_name'=>'sanctum']);
        $dua=Permission::create(['name'=>'Show Receptionist','guard_name'=>'sanctum']);
        $tiga=Permission::create(['name'=>'Show Keperluan','guard_name'=>'sanctum']);
        $empat=Permission::create(['name'=>'Show User Management','guard_name'=>'sanctum']);

        $roles = [
            ['name' => 'Admin','guard_name'=>'sanctum'],
            ['name' => 'Management','guard_name'=>'sanctum'],
            ['name' => 'Operation','guard_name'=>'sanctum'],
        ];
        Role::insert($roles);

        $admin = Role::where('name','Admin')->first();
        $admin->givePermissionTo($satu);
        $admin->givePermissionTo($dua);
        $admin->givePermissionTo($tiga);
        $admin->givePermissionTo($empat);


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
