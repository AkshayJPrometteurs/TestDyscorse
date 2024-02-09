<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\UserMorningCheckIN;
use Illuminate\Http\Request;

class UserMorningCheckInController extends Controller
{
    public function user_morning_check_in_store(Request $request){
        $request->validate(['user_feelings' => 'required','three_things_for_today' => 'required','non_negotiables_tasks' => 'required','todays_doing' => 'required']);
        $request->merge(['non_negotiables_tasks'=>implode(',',$request->non_negotiables_tasks)]);
        UserMorningCheckIN::create($request->all());
        return response()->json(['status'=>200,'data'=>[],'message'=>'Data Saved Successfully']);
    }
}
