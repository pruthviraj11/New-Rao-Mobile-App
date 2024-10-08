<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientType extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'client_types';

    protected $fillable = [
        'name',
        'displayname',
        'status',
        'created_by',
    ];
}
