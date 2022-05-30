<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleSuperAdmin = Role::where('name', 'Super Admin')->first()->_id;
        self::checkIssetBeforeCreate([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '0961130648',
            'password' => Hash::make('123456'),
            'role_id' => $roleSuperAdmin,
            'address' => 'Vĩnh Tường, VĨnh Phúc',
            'gender' => 1,
            'date_of_birth' => 1642210565
        ]);
    }

    public function checkIssetBeforeCreate($data) {
        $admin = Admin::where('email', $data['email'])->first();
        if (empty($admin)) {
            Admin::create($data);
        } else {
            $admin->update($data);
        }
    }
}
