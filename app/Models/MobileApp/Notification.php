<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','assigned_user_id','member_id','task_id','task_title','task_desc','notification_type','task_time','notification_status','user_temp_mobile'];
}
