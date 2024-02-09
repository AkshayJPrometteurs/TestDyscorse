<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddMembers extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'member_name',
        'member_email',
        'member_mobile',
    ];
}
