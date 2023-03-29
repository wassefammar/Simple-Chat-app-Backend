<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = [
        'created_by',
        'name',
        'is_private'
    ];
    protected $table="chats";
    public function participants(): HasMany{
        return $this->hasMany(ChatParticipant::class, 'chat_id');
    }

    public function messages(): HasMany{
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    public function lastmessages(): HasOne{
        return $this->hasOne(ChatMessage::class, 'chat_id')->latest('updated_at');
    }

    public function scopeHasParticipants($query, int $userId){
        return $query->whereHas('participants', function($q) use ($userId){
            $q->where('user_id',$userId);
        });
    }
}
