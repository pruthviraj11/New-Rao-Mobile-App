<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Draws extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'draws';
    protected $fillable = [
        'date',
        'crs_cutoff',
        'type',
        'ita_issue',
        'status',
    ];
    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'client_type');
    }
}
