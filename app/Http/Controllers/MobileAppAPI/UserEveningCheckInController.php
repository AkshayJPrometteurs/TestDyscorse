<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\UserEveningCheckIN;
use Illuminate\Http\Request;

class UserEveningCheckInController extends Controller
{
    public function user_evening_check_in_store(Request $request){
        $request->validate(['user_feelings' => 'required','three_things_for_today' => 'required']);
        UserEveningCheckIN::create($request->all());
        return response()->json(['status'=>200,'data'=>[],'message'=>'Data Saved Successfully']);
    }
}
