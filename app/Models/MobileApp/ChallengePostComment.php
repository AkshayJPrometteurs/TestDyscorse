<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengePostComment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','challenge_comment_date','challenge_id','post_id','comment'];
}
