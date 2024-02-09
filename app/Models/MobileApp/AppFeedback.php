<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppFeedback extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','feedback'];
}
