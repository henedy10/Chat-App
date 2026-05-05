<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Chat\Message;
class Conversation extends Model
{
    protected $fillable = [
        'user1_id',
        'user2_id'
    ];

    public function user1(){
        return $this->belongsTo(User::class,'user1_id');
    }

    public function user2(){
        return $this->belongsTo(User::class,'user2_id');
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function lastMessage(){
        return $this->hasOne(Message::class)->latest();
    }
}
