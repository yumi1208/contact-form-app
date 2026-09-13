<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_contact_list(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'list@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストマンション101',
            'category_id' => $category->id,
            'detail' => '一覧APIテスト',
        ]);

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'email' => 'list@example.com',
        ]);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_can_get_contact_detail(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $contact = Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'detail@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => null,
            'category_id' => $category->id,
            'detail' => '詳細APIテスト',
        ]);

        $contact->tags()->attach($tag->id);

        $response = $this->getJson(
            '/api/v1/contacts/' . $contact->id
        );

        $response->assertStatus(200);

        $response->assertJsonPath(
            'data.email',
            'detail@example.com'
        );

        $response->assertJsonPath(
            'data.category.content',
            '商品のお届けについて'
        );
    }

    public function test_can_create_contact(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $data = [
            'first_name' => '花子',
            'last_name' => '佐藤',
            'gender' => 2,
            'email' => 'create@example.com',
            'tel' => '09012345678',
            'address' => '福岡県福岡市',
            'building' => 'テストビル101',
            'category_id' => $category->id,
            'detail' => 'APIから作成しました',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->postJson(
            '/api/v1/contacts',
            $data
        );

        $response->assertStatus(201);

        $response->assertJsonPath(
            'data.email',
            'create@example.com'
        );

        $this->assertDatabaseHas('contacts', [
            'email' => 'create@example.com',
        ]);

        $contact = Contact::where(
            'email',
            'create@example.com'
        )->first();

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_can_update_contact(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '要望',
        ]);

        $contact = Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'before@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => null,
            'category_id' => $category->id,
            'detail' => '更新前',
        ]);

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'after@example.com',
            'tel' => '09012345678',
            'address' => '福岡県福岡市',
            'building' => '更新後ビル',
            'category_id' => $category->id,
            'detail' => '更新後',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->putJson(
            '/api/v1/contacts/' . $contact->id,
            $data
        );

        $response->assertStatus(200);

        $response->assertJsonPath(
            'data.email',
            'after@example.com'
        );

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'email' => 'after@example.com',
            'detail' => '更新後',
        ]);

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_can_delete_contact(): void
    {
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
            'detail' => '削除APIテスト',
        ]);

        $response = $this->deleteJson(
            '/api/v1/contacts/' . $contact->id
        );

        $response->assertStatus(204);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_store_validation_returns_422(): void
    {
        $response = $this->postJson(
            '/api/v1/contacts',
            []
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'address',
            'category_id',
            'detail',
        ]);
    }

    public function test_nonexistent_contact_returns_404(): void
    {
        $response = $this->getJson(
            '/api/v1/contacts/999999'
        );

        $response->assertStatus(404);
    }
}