<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_contacts(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => null,
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です',
        ]);

        $this->assertCount(1, $category->contacts);
    }

    public function test_contact_belongs_to_category(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test2@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => null,
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です',
        ]);

        $this->assertEquals(
            $category->id,
            $contact->category->id
        );
    }

    public function test_contact_belongs_to_many_tags(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::create([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test3@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => null,
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $contact->tags()->attach($tag->id);

        $this->assertTrue(
            $contact->tags->contains($tag)
        );
    }
}