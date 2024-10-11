<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FCMTokens extends Model
{
    use HasFactory;
    protected $table = 'fcm_tokens';
    protected $fillable = [
        'token',
        'device_id',
        // 'order',
        // 'category_id',
        // 'slug',
        // 'status',
    ];
    // public function clientType()
    // {
    //     return $this->belongsTo(ClientType::class, 'client_type');
    // }
}
