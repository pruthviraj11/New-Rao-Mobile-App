<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advisor extends Model
{
    use HasFactory;
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'user_category',
        'password',
        'phone_number',
        'reporting_to',
        'is_download',
        'download_date',
        'role_id',
        'avatar',
        'status',
    ];
    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'client_type');
    }
}
