<?php

namespace Tests\Feature;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Storage;
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

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $response = $this->actingAs($user)->get("/login");
        $response->assertStatus(302);
        $response->assertRedirect(route("chat.index"));
    }

    public function test_logout_returns_302_as_guest(): void
    {
        $response = $this->get("/logout");
        $response->assertStatus(302);
        $response->assertRedirect(route("index"));
    }

    public function test_logout_returns_302_as_user(): void
    {
        $user = User::factory()->create();

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $response = $this->actingAs($user)->get("/logout");
        $response->assertStatus(302);
        $response->assertRedirect(route("index"));
    }

    public function test_register_returns_200_as_guest(): void
    {
        $response = $this->get("/register");
        $response->assertStatus(200);
    }

    public function test_register_returns_302_as_user(): void
    {
        $user = User::factory()->create();

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $response = $this->actingAs($user)->get("/register");
        $response->assertStatus(302);
        $response->assertRedirect(route("chat.index"));
    }

    public function test_chat_returns_302_as_guest(): void
    {
        $response = $this->get("/chat");
        $response->assertStatus(302);
        $response->assertRedirect(route("login"));
    }

    public function test_chat_returns_200_as_user(): void
    {
        $user = User::factory()->create();

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $response = $this->actingAs($user)->get("/chat");
        $response->assertStatus(200);
    }

    public function test_guest_can_register(): void
    {
        Storage::fake("public");

        $data = [
            "nickname" => "test_user",
            "email" => "test@test.com",
            "password" => "testpassword",
            "profile_picture" => $file = File::image("image.jpg"),
        ];

        // Send POST request to store route
        $response = $this->post(route("users.store"), $data);

        // Assert response status
        $response->assertStatus(302);
        $response->assertRedirect(route("chat.index"));

        // Check, if user was created in db
        $this->assertDatabaseHas("users", [
            "email" => "test@test.com"
        ]);

        // Check, if file exists and thus upload was successful
        Storage::disk("public")->assertExists(str_replace("storage/", "", User::first()->profile_picture));
    }

    public function test_guest_can_login(): void
    {
        // Create fake user
        $user = User::factory()->create([
            "password" => Hash::make("testpassword")
        ]);

        $data = [
            "email" => $user->email,
            "password" => "testpassword",
        ];

        // Send POST request to login route
        $response = $this->post(route("users.login", $data));

        // Assert response status
        $response->assertStatus(302);
        $response->assertRedirect(route("chat.index"));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        // Send POST request to logout route
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $response = $this->actingAs($user)->post(route("users.logout"));

        // Assert response status
        $response->assertStatus(302);
        $response->assertRedirect(route("index"));

        $this->assertGuest();
    }
}
