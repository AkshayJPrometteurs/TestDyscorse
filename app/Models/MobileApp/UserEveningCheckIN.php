<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEveningCheckIN extends Model
{
    use HasFactory;
    protected $table = "user_evening_checkin";
    protected $fillable = ['user_id','user_feelings','three_things_for_today'];
}
