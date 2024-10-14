<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatSession extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'chat_session_messages';

    protected $fillable = [
        'created_at',
        'updated_at',
        'advisor_id',
        'client_id',
        'status',
        'advisors',
        'type',
        'is_close_system',
        'message_sent'
    ];

    public function chatSessionMessages()
    {
        return $this->hasMany(ChatSessionMessage::class, 'chat_session_id');
    }

    public function advisor()
    {
        return $this->hasOne(User::class, 'id', 'advisor_id');
    }

    public function chatSessionMessage()
    {
        return $this->hasOne(ChatSessionMessage::class);
    }

}