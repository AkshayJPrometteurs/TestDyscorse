<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengePostLike extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','challenge_id','post_id','like_status'];
}
