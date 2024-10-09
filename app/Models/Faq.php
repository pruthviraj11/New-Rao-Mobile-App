<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'faqs';
    protected $fillable = [
        'faq_category_id',
        'title',
        'answer',
        'category_id',
        'status',
        'sequence',
    ];
    public function faq_categories()
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id'); // Use 'category_id' instead of 'client_type'
    }
}
