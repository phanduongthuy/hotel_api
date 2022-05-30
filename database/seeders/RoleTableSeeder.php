<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        $permission = Permission::where('name', 'super-admin')->first();
        $roleAdmin = Role::create([
            'name' => 'Super Admin',
            'is_protected' => true,
            'description' => 'Quản trị hệ thống',
        ]);
        $roleAdmin->permissions()->attach($permission->_id);
    }
}
