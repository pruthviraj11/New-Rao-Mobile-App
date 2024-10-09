<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'faq_categories';
    protected $fillable = [
        'name',
        'description',
        'status',
        'category_id',
    ];
}
