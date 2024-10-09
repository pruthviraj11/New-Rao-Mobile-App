<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationStatuses extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'application_statuses';
    protected $fillable = [
        'name',
        'description',
        'order',
        'category_id',
        'slug',
        'status',
    ];
    // public function clientType()
    // {
    //     return $this->belongsTo(ClientType::class, 'client_type');
    // }
}
