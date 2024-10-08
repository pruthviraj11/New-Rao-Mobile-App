<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'news';
    protected $fillable = [
        'category_id',
        'title',
        'short_description',
        'long_description',
        'file',
        'date',
        'news_button_text',
        'status',
    ];
}
