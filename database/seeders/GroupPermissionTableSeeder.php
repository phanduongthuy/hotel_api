<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermissionGroup;

class GroupPermissionTableSeeder extends Seeder
{
    public function checkIssetBeforeCreate($data) {
        $groupPermission = PermissionGroup::where('code', $data['code'])->first();
        if (empty($groupPermission)) {
            PermissionGroup::create($data);
        } else {
            $groupPermission->update($data);
        }
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        self::checkIssetBeforeCreate([
            'name' => 'Chăm sóc khách hàng',
            'code' => 'customer-care',
            'description' => 'Quản lý toàn bộ chức năng liên quan đến chăm sóc khách hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Quản lý dịch',
            'code' => 'translate-management',
            'description' => 'Quản lý toàn bộ chức năng liên quan đến việc dịch'
        ]);
    }
}
