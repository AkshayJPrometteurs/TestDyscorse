<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
      	'event_id',
        'date',
        'day',
        'assign_task_type',
        'wake_up_time',
        'sleep_time',
        'task_type',
        'task_name',
        'task_start_time',
        'task_end_time',
      	'task_assign_status',
        'task_assign_id',
        'task_status',
      	'task_reschedule_reason',
      	'task_reschedule_comment'
    ];
}
