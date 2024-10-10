<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeService extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'home_services';
    protected $fillable = [
        'title',
        'description',
        'file',
        'status',
        'service_image',
    ];
}
