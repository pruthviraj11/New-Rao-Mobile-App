<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatSessionMessage extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'chat_session_messages';

    protected $fillable = [
        'chat_session_id',
        'message',
        'sender_id',
        'receiver_id',
        'created_at',
        'updated_at',
        'status',
        'type',
        'additional_type',
        'send_by',
        'is_notify'
    ];
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function chatSession()
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }
}
