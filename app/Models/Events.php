<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Events extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'events';
    protected $fillable = [
        'title',
        'status',
    ];
    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'client_type');
    }
}
