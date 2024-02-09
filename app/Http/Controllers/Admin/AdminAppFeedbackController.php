<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\AppFeedback;
use Illuminate\Http\Request;

class AdminAppFeedbackController extends Controller
{
    public function feedback_list(){
        $feedback = AppFeedback::join('users','users.id','user_id')
        ->select('app_feedback.*','users.first_name','users.last_name')
        ->get();
        return view('Admin.feedback.feedback-list',compact('feedback'));
    }

    public function edit_feedback($id){
        $feedback = AppFeedback::join('users','users.id','user_id')
        ->select('app_feedback.*','users.first_name','users.last_name')
        ->where('app_feedback.id',$id)
        ->first();
        return view('Admin.feedback.edit-feedback',compact('feedback'));
    }

    public function update_feedback(Request $request, $id){
        $request->validate(['feedback'=>'required']);
        AppFeedback::find($id)->update(['feedback'=>$request->feedback]);
        flash()->addSuccess('Feedback Updated Successfully');
        return redirect()->route('feedback_list');
    }

    public function delete_feedback(Request $request){
        AppFeedback::find($request->id)->delete();
        flash()->addSuccess('Feedback Deleted Successfully');
        return redirect()->route('feedback_list');
    }
}
