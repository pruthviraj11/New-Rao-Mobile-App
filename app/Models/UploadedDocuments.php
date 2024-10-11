<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadedDocuments extends Model
{
    use HasFactory;
    protected $table = 'uploaded_documents';
    protected $fillable = [
        'user_id',
        'file_name',
        'document_type',
        'default_doc_id',
    ];
    // public function clientType()
    // {
    //     return $this->belongsTo(ClientType::class, 'client_type');
    // }
}
