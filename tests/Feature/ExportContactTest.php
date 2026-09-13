<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_export_contacts_as_csv(): void
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
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストマンション101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/contacts/export');

        $response->assertStatus(200);

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'taro@example.com',
            $content
        );

        $this->assertStringContainsString(
            '太郎',
            $content
        );
    }

    public function test_csv_export_respects_search_conditions(): void
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
            'detail' => '太郎の問い合わせ',
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
            'detail' => '花子の問い合わせ',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/contacts/export?keyword=太郎');

        $response->assertStatus(200);

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            'taro@example.com',
            $content
        );

        $this->assertStringNotContainsString(
            'hanako@example.com',
            $content
        );
    }
}