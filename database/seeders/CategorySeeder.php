<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ← 追加

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['content' => '商品のお届けについて'],
            ['content' => '商品交換について'],
            ['content' => '商品トラブル'],
            ['content' => 'ショップへのお問い合わせ'],
            ['content' => 'その他'],
        ]);
    }
}