<?php

use App\Models\Chat\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return true;
});

Broadcast::channel('user.{userId}', function($user, $userId){
    return $user->id == $userId;
});