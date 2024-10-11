<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDocuments extends Model
{
    use HasFactory;
    protected $table = 'user_documents';
    protected $fillable = [
        'user_id',
        'imm_no',
        'app_no',
        'doc_id',
        'doc_name',
        'upload_date',
        'doc_file_name',
        'comments',
        'status'
    ];
    // public function clientType()
    // {
    //     return $this->belongsTo(ClientType::class, 'client_type');
    // }
}
