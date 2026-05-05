<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Chat\Conversation;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'message',
        'sender_id'
    ];

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function sender(){
        return $this->belongsTo(User::class,'sender_id');
    }
}
