<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ApplicationStatuses;

class UserApplicationStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_application_status';
    protected $fillable = [
        'user_id',
        'status_value',
        'application_status',
        'status_date',
        'created_at',
        'updated_at',
        'status_order'
    ];

    public function applicationStatus()
    {
        return $this->belongsTo(ApplicationStatuses::class, 'application_status', 'id');
    }
}
