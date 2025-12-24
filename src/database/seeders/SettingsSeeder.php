<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'contact_unlock_fee',
                'value' => '50000',
                'type' => 'decimal',
                'group' => 'payment',
                'description' => 'Phí mở khóa thông tin liên hệ học sinh (VNĐ)'
            ],
            [
                'key' => 'payment_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => 'Bật/tắt tính năng thanh toán'
            ],
            [
                'key' => 'vnpay_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => 'Kích hoạt VNPay'
            ],
            [
                'key' => 'momo_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => 'Kích hoạt MoMo'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
