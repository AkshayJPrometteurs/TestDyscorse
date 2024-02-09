<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilestonePointsCalculations extends Model
{
    use HasFactory;
    protected $fillable = ['milestone_start','milestone_end','milestone_points'];
}
