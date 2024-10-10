<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurServices extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'our_services';
    protected $fillable = [
        'title',
        'short_description',
        'long_description',
        'date',
        'contact_no',
        'file',
        'background_color',
        'text_color',
        'status',
    ];
    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'client_type');
    }
}
