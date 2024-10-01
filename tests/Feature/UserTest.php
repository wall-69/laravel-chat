<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_200_as_guest(): void
    {
        $response = $this->get("/login");
        $response->assertStatus(200);
    }

    public function test_login_returns_302_as_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/login");
        $response->assertStatus(302);
    }

    public function test_logout_returns_302_as_guest(): void
    {
        $response = $this->get("/logout");
        $response->assertStatus(302);
    }

    public function test_logout_returns_302_as_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/logout");
        $response->assertStatus(302);
    }

    public function test_register_returns_200_as_guest(): void
    {
        $response = $this->get("/register");
        $response->assertStatus(200);
    }

    public function test_register_returns_302_as_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/register");
        $response->assertStatus(302);
    }

    public function test_chat_returns_302_as_guest(): void
    {
        $response = $this->get("/chat");
        $response->assertStatus(302);
    }

    public function test_chat_returns_200_as_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/chat");
        $response->assertStatus(200);
    }
}
