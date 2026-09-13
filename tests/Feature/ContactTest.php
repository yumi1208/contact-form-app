<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_can_be_displayed(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_contact_confirmation_can_be_displayed(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'confirm@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストマンション101',
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
            'detail' => '確認画面のテストです',
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertStatus(200);

        $response->assertSee('太郎');
        $response->assertSee('山田');
        $response->assertSee('confirm@example.com');
        $response->assertSee('商品のお届けについて');
        $response->assertSee('質問');
        $response->assertSee('確認画面のテストです');
    }

    public function test_contact_can_be_stored(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストマンション101',
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
            'detail' => 'お問い合わせ内容です',
        ];

        $response = $this->post('/contacts', $data);

        $response->assertRedirect('/thanks');

        $this->assertDatabaseHas('contacts', [
            'first_name' => '太郎',
            'last_name' => '山田',
            'email' => 'test@example.com',
        ]);

        $contactId = Contact::where(
            'email',
            'test@example.com'
        )->value('id');

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contactId,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_thanks_page_can_be_displayed(): void
    {
        $response = $this->get('/thanks');

        $response->assertStatus(200);
    }
}