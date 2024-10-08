<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'news_categories';
    protected $fillable = [
        'title',
        'status',
        'client_type',
    ];
    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'client_type');
    }
}
