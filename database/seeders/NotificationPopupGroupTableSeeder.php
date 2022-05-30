<?php

namespace Database\Seeders;

use App\Models\NotificationPopupGroup;
use Illuminate\Database\Seeder;

class NotificationPopupGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        self::checkIssetBeforeCreate([
            'name' => 'Our Services',
            'code' => 'our-services',
            'description' => 'Quản lý toàn bộ thông báo của trang Our Services'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Cart',
            'code' => 'cart',
            'description' => 'Quản lý toàn bộ thông báo của trang Cart'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Payment',
            'code' => 'payment',
            'description' => 'Quản lý toàn bộ thông báo của trang Payment'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Supports',
            'code' => 'support',
            'description' => 'Quản lý toàn bộ thông báo của trang Supports'
        ]);
    }

    public function checkIssetBeforeCreate($data) {
        $notificationPopupGroup = NotificationPopupGroup::where('code', $data['code'])->first();
        if (empty($notificationPopupGroup)) {
            NotificationPopupGroup::create($data);
        } else {
            $notificationPopupGroup->update($data);
        }
    }
}
