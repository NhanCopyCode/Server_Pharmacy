<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TargetAudienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('target_audiences')->insert([
            ['name' => 'Trẻ em'],
            ['name' => 'Người lớn tuổi'],
            ['name' => 'Phụ nữ mang thai'],
            ['name' => 'Người già'],
            ['name' => 'Người chơi thể thao'],
        ]);
    }
}
