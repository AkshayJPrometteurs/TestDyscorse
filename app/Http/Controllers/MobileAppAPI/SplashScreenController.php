<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\SplashScreenQuestions;

class SplashScreenController extends Controller
{
    public function splash_screen_questions(){
        $questions = SplashScreenQuestions::where('status','active')->get();
        return response()->json([
            'status' => 200,
            'data' => ['questions' => $questions],
            'message' => 'Questions Fetched Successfully'
        ]);
    }
}
