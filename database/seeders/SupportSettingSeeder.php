<?php

namespace Database\Seeders;

use App\Models\SupportSetting;
use Illuminate\Database\Seeder;

class SupportSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'auto_reply_message',
                'value' => 'Hello! Thank you for contacting Holy Cross Parish. Our admin is currently offline. We will respond to your message as soon as possible. God bless!',
                'description' => 'Auto-reply message kapag offline ang admin',
            ],
            [
                'key' => 'auto_reply_enabled',
                'value' => '1',
                'description' => 'I-enable o i-disable ang auto-reply',
            ],
            [
                'key' => 'office_hours',
                'value' => 'Monday to Saturday: 8:00 AM - 5:00 PM',
                'description' => 'Office hours ng parish',
            ],
            [
                'key' => 'contact_number',
                'value' => '+63 968 676 9234',
                'description' => 'Contact number ng parish',
            ],
            [
                'key' => 'contact_email',
                'value' => 'holycrossparish.app@gmail.com',
                'description' => 'Email ng parish',
            ],
            [
                'key' => 'contact_address',
                'value' => 'J. Ramos Street 267, Poblacion West, Asingan, Pangasinan, 2439',
                'description' => 'Address ng parish',
            ],
        ];

        foreach ($settings as $setting) {
            SupportSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✅ Support settings seeded!');
    }
}