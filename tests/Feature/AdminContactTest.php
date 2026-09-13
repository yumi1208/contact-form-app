<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_contact_detail(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'detail@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
            'category_id' => $category->id,
            'detail' => '詳細表示テストです',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin/contacts/' . $contact->id);

        $response->assertStatus(200);

        $response->assertSee('太郎');
        $response->assertSee('山田');
        $response->assertSee('detail@example.com');
        $response->assertSee('商品のお届けについて');
        $response->assertSee('詳細表示テストです');
    }

    public function test_authenticated_user_can_delete_contact(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'delete@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => null,
            'category_id' => $category->id,
            'detail' => '削除テストです',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/admin/contacts/' . $contact->id);

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}