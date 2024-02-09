<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompleteChallenge extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','challenge_id','challenge_status','challenge_completed_date'];
}
