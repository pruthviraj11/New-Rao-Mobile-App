<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuccessStories extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'success_stories';
    protected $fillable = [
        'title',
        'short_description',
        'long_description',
        'date',
        'candidate_name',
        'candidate_image',
        'candidate_type',
        'for_home',
        'ratings',
        'video_thumbnail',
        'file',
        'status',
    ];
    public function clientType()
    {
        return $this->belongsTo(ClientType::class, 'client_type');
    }
}
