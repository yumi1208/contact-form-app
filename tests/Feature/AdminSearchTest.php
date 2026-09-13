<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_search_contacts_by_keyword(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'taro@example.com',
            'tel' => '09011111111',
            'address' => '東京都',
            'building' => null,
            'category_id' => $category->id,
            'detail' => 'テスト1',
        ]);

        Contact::create([
            'first_name' => '花子',
            'last_name' => '佐藤',
            'gender' => 2,
            'email' => 'hanako@example.com',
            'tel' => '09022222222',
            'address' => '大阪府',
            'building' => null,
            'category_id' => $category->id,
            'detail' => 'テスト2',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin?keyword=太郎');

        $response->assertStatus(200);

        $response->assertSee('太郎');
        $response->assertDontSee('花子');
    }

    public function test_authenticated_user_can_filter_contacts_by_gender(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'male@example.com',
            'tel' => '09033333333',
            'address' => '東京都',
            'building' => null,
            'category_id' => $category->id,
            'detail' => '男性データ',
        ]);

        Contact::create([
            'first_name' => '花子',
            'last_name' => '佐藤',
            'gender' => 2,
            'email' => 'female@example.com',
            'tel' => '09044444444',
            'address' => '大阪府',
            'building' => null,
            'category_id' => $category->id,
            'detail' => '女性データ',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin?gender=1');

        $response->assertStatus(200);

        $response->assertSee('太郎');
        $response->assertDontSee('花子');
    }

    public function test_admin_page_is_paginated(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        for ($i = 1; $i <= 8; $i++) {
            Contact::create([
                'first_name' => '太郎' . $i,
                'last_name' => '山田',
                'gender' => 1,
                'email' => 'user' . $i . '@example.com',
                'tel' => '09012345678',
                'address' => '東京都',
                'building' => null,
                'category_id' => $category->id,
                'detail' => 'テスト' . $i,
            ]);
        }

        $response = $this
            ->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);

        $response->assertSee('太郎1');
        $response->assertDontSee('太郎8');
    }
}