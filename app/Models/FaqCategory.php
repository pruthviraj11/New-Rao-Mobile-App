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

    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'category_id'); // Use 'category_id' instead of 'client_type'
    }

}
