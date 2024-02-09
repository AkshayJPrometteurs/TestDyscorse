<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SplashScreenQuestions extends Model
{
    use HasFactory;
    protected $fillable = ['questions','questions_slug','status'];
}
