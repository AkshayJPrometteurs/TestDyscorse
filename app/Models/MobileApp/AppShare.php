<?php

namespace App\Models\MobileApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppShare extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','shared_email'];
}
