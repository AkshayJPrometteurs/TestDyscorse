<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendsFollowers extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','friend_user_id','followers_status'];
}
