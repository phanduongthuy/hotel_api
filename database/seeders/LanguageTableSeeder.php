<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::truncate();

        Language::create([
            'name' => 'Vietnamese',
            'description' => 'Tiếng Việt'
        ]);
        Language::create([
            'name' => 'English',
            'description' => 'Tiếng Anh'
        ]);
        Language::create([
            'name' => 'France',
            'description' => 'Tiếng Pháp'
        ]);
        Language::create([
            'name' => 'German',
            'description' => 'Tiếng Đức'
        ]);
        Language::create([
            'name' => 'Japanese',
            'description' => 'Tiếng Nhật'
        ]);
        Language::create([
            'name' => 'Korean',
            'description' => 'Tiếng Hàn'
        ]);
        Language::create([
            'name' => 'Chinese',
            'description' => 'Tiếng Trung'
        ]);
    }
}
