<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat>
 */
class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "picture" => "img/chat/default_chat_picture.svg",
            "is_private" => false,
        ];
    }

    public function DM(): Factory
    {
        return $this->state(function (array $attributes) {
            return ["type" => "dm"];
        });
    }

    public function channel(): Factory
    {
        return $this->state(function (array $attributes) {
            return ["type" => "channel"];
        });
    }
}
