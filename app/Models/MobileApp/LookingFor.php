<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookingFor extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'looking_for_title',
        'looking_for_slug',
        'looking_for_status',
    ];
}
