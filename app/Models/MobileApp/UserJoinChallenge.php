<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJoinChallenge extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','user_challenge_joinned_date','challenge_id','challenge_time'];
}
