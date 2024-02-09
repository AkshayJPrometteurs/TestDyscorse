<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMorningCheckIN extends Model
{
    use HasFactory;
    protected $table = "user_morning_checkin";
    protected $fillable = ['user_id','user_feelings','three_things_for_today','non_negotiables_tasks','todays_doing'];
}
