<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefaultDocuments extends Model
{
    use HasFactory;
    protected $table = 'default_documents';
    protected $fillable = [
        'doc_user_category_id',
        'doc_id',
        'doc_type_id',
        'doc_category_name',
        'is_active',
    ];
    // public function clientType()
    // {
    //     return $this->belongsTo(ClientType::class, 'client_type');
    // }
}
