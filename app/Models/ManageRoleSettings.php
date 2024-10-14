<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageRoleSettings extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'manage_role_setting';

    protected $fillable = [
        'full_access',
        'partial_access',
        'restriction_access',
        'dymanager_manager',
        'pearo',
        'adviser',
        'client',
    ];
}
