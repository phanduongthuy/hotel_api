<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    public function checkIssetBeforeCreate($data)
    {
        $permission = Permission::where('name', $data['name'])->first();
        if (empty($permission)) {
            Permission::create($data);
        } else {
            $permission->update($data);
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SuperAdmin
        self::checkIssetBeforeCreate([
            'name' => 'super-admin',
            'display_name' => 'Toàn bộ quyền',
            'group_code' => null,
            'description' => 'Có toàn quyền sử dụng hệ thống'
        ]);

        // Order
        self::checkIssetBeforeCreate([
            'name' => 'get-request-translate',
            'display_name' => 'Danh sách yêu cầu dịch',
            'group_code' => 'translate-management',
            'description' => 'Xem danh sách yêu cầu dịch'
        ]);
        self::checkIssetBeforeCreate([
            'name' => 'quote-request-translate',
            'display_name' => 'Báo giá bản dịch',
            'group_code' => 'translate-management',
            'description' => 'Báo giá bản dịch'
        ]);
        self::checkIssetBeforeCreate([
            'name' => 'send-translate',
            'display_name' => 'Gửi bản dịch',
            'group_code' => 'translate-management',
            'description' => 'Gửi bản dịch hoàn chỉnh cho khách'
        ]);

        // Customer care
        self::checkIssetBeforeCreate([
            'name' => 'get-request-support',
            'display_name' => 'Danh sách yêu cầu hỗ trợ',
            'group_code' => 'customer-care',
            'description' => 'Xem danh sách yêu cầu hỗ trợ'
        ]);
        self::checkIssetBeforeCreate([
            'name' => 'respone-request-support',
            'display_name' => 'Phản hồi yêu cầu hỗ trợ',
            'group_code' => 'customer-care',
            'description' => 'Phản hồi yêu cầu hỗ trợ'
        ]);
    }
}
