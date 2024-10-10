<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherStakeholders extends Model
{
    use HasFactory;
    protected $table = 'other_stakeholders';
    protected $fillable = [
        'user_id',
        'attached_user_id',
        'role_name',
    ];
    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'client_type');
    }
}
