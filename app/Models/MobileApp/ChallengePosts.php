<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengePosts extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','challenge_post_date','challenge_id','post_caption','post_image'];
}
