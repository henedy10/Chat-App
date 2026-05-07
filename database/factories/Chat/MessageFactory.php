<?php

namespace Database\Factories\Chat;

use App\Models\Chat\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;
    public function definition(): array
    {
        return [
            'conversation_id' =>3,
            'sender_id' =>1,
            'message' => $this->faker->sentence,
            'is_read' => $this->faker->boolean,
        ];
    }
}
