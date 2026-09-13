<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_admin(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);
    }
}