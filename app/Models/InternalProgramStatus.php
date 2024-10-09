<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalProgramStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'internal_program_statuses';

    protected $fillable = [
        'name',
        'description',
        'status',
        'order',
        'category_id',
    ];

    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'category_id');
    }
}
