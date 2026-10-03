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
                'value' => 'Monday only: 8:00 AM - 4:00 PM',
                'description' => 'Office hours ng parish',
            ],
            [
                'key' => 'contact_email',
                'value' => 'holycrossparish.app@gmail.com',
                'description' => 'Email ng parish',
            ],
            [
                'key' => 'contact_address',
                'value' => 'Poblacion West Alcala Pangasinan, 2425',
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