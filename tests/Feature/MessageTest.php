<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\User;
use App\Models\UserChat;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cant_send_message(): void
    {
        Event::fake();

        $chat = Chat::factory()->channel()->create();

        $data = [
            "message" => fake()->sentence()
        ];

        // Send POST request to store route with message
        $response = $this->post(route("messages.store", ["chat" => $chat->id]), $data);

        // Assert response status & json message TODO: MAKE API AND ADD OTHER STATUS CODE OR SOMETHING
        $response->assertStatus(302);

        // Assert event was broadcasted
        Event::assertNotDispatched(MessageSent::class);
    }

    public function test_user_can_send_message(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $chat = Chat::factory()->channel()->create();
        UserChat::create(["user_id" => $user->id, "chat_id" => $chat->id]);

        $data = [
            "message" => fake()->sentence()
        ];

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        // Send POST request to store route without message 
        $response = $this->actingAs($user)->post(route("messages.store", ["chat" => $chat->id]), []);
        // Assert validation error TODO: MAKE THIS ROUTE API PLEASE
        $response->assertStatus(302);

        // Send POST request to store route with message
        $response = $this->actingAs($user)->post(route("messages.store", ["chat" => $chat->id]), $data);

        // Assert response status & json message
        $response
            ->assertStatus(200)
            ->assertJson([
                "message" => "Message was successfully stored.",
            ]);

        // Assert that the database contains the message from this user in this chat
        $this->assertDatabaseHas("messages", [
            "user_id" => $user->id,
            "chat_id" => $chat->id,
            "content" => $data["message"]
        ]);

        // Assert event was broadcasted
        Event::assertDispatched(MessageSent::class);
    }
}
