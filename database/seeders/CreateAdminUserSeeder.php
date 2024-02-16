<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user1 = User::create([
            'name' => 'mohamed',
            'email' => 'Admin@gmail.com',
            'password' => bcrypt('roka'),
            'roles_name' => ["Admin"],
            'Status' => 'Active',
        ]);




        $role1 = Role::create(['name' => 'Admin']);
        $permissions = Permission::pluck('id','id')->all();


        $role1->syncPermissions($permissions);


        $user1->assignRole([$role1->id]);
    }
}
