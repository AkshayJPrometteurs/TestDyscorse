<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenges extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','challenge_date','challenges_title','challenges_desc','challenges_image','challenge_time'];
}
