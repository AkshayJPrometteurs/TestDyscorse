<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointCalculations extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_completion',
        'max_streak',
        'se_follow',
        'se_assigning_task_to_family_member',
        'feedback',
        'app_sharing',
        'reflection_and_review',
        'total_category_completion',
        'category_completion',
    ];
}
