<?php

namespace Database\Seeders;

use App\Models\EmailTemplateGroup;
use Illuminate\Database\Seeder;

class GroupEmailTemplateTableSeeder extends Seeder
{
    public function checkIssetBeforeCreate($data) {
        $groupPermission = EmailTemplateGroup::where('code', $data['code'])->first();
        if (empty($groupPermission)) {
            EmailTemplateGroup::create($data);
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
            'name' => 'Khách hàng yêu cầu dịch/review',
            'code' => 'email-request-success',
            'description' => 'Email thông báo khi khách hàng yêu cầu dich/review'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Khách hàng thay đổi file yêu cầu dịch/review',
            'code' => 'email-update-success',
            'description' => 'Email thông báo khi khách hàng thay đổi file yêu cầu dich/review'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Thông tin thanh toán',
            'code' => 'email-payment-info',
            'description' => 'Email thông tin thanh toán cho khách hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Phản hồi khách hàng',
            'code' => 'email-respone-customer',
            'description' => 'Email phản hồi yêu cầu của khách hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Xác thực email',
            'code' => 'email-verify',
            'description' => 'Email thông tin xác thực email'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Admin cập nhật đơn hàng',
            'code' => 'admin-update-order',
            'description' => 'Quản trị viên cập nhật báo giá, cập nhật file kết quả'
        ]);
    }
}
