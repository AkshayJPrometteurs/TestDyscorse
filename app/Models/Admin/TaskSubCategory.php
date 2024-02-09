<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'task_category_name',
        'task_sub_category_name',
        'task_sub_category_slug',
        'task_sub_category_image',
        'task_sub_category_status',
    ];
}
