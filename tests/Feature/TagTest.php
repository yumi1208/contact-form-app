<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/admin/tags', [
                'name' => '新しいタグ',
            ]);

        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'name' => '新しいタグ',
        ]);
    }

    public function test_tag_can_be_updated(): void
    {
        $user = User::factory()->create();

        $tag = Tag::create([
            'name' => '変更前タグ',
        ]);

        $response = $this
            ->actingAs($user)
            ->put('/admin/tags/' . $tag->id, [
                'name' => '変更後タグ',
            ]);

        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '変更後タグ',
        ]);
    }

    public function test_tag_can_be_deleted(): void
    {
        $user = User::factory()->create();

        $tag = Tag::create([
            'name' => '削除用タグ',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/admin/tags/' . $tag->id);

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }
}