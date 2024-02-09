<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\SplashScreenQuestions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSplashScreenQuestionsController extends Controller
{
    public function splash_screen_questions(){
        $ssq = SplashScreenQuestions::all();
        return view('Admin.splash-screen-questions.splashscreenquestions',compact('ssq'));
    }

    public function add_question(){
        return view('Admin.splash-screen-questions.add-ssq');
    }

    public function save_question(Request $request){
        $request->validate(['questions'=>'required|unique:splash_screen_questions,questions']);
        SplashScreenQuestions::create([
            'questions' => $request->questions,
            'questions_slug' => Str::slug($request->questions),
            'status' => 'active',
        ]);
        flash()->addSuccess('Question Added Successfully');
        return redirect()->route('splash_screen_questions');
    }

    public function edit_question($slug){
        $ssq = SplashScreenQuestions::where('questions_slug',$slug)->first();
        return view('Admin.splash-screen-questions.edit-ssq',compact('ssq'));
    }

    public function update_question(Request $request, $id){
        $request->validate(['questions' => 'required|unique:splash_screen_questions,questions,'.$id.'']);
        $ssq = SplashScreenQuestions::find($id);
        $ssq->update(['questions'=>$request->questions,'questions_slug'=>Str::slug($request->questions)]);
        flash()->addSuccess('Question Updated Successfully');
        return redirect()->route('splash_screen_questions');
    }

    public function delete_question(Request $request){
        SplashScreenQuestions::find($request->id)->delete();
        flash()->addSuccess('Question Deleted Successfully');
        return redirect()->route('splash_screen_questions');
    }
  
  	public function ssq_user_answers_list(){
        $user = User::all();
        foreach($user as $data){
            if(!empty($data->splash_que_ans)){
                $ssq = explode(',',$data->splash_que_ans);
                $ssq_user_ans_count = count($ssq);
            }else{$ssq_user_ans_count = 0;}
            $ans_data[] = [
                'user_id' => $data->id,
                'name' => ucfirst($data->first_name)." ".ucfirst($data->last_name),
                'ans' => $ssq_user_ans_count
            ];
        }
        return view('Admin.splash-screen-questions.ssq-user-answers-list',compact('ans_data'));
    }

    public function ssq_view_user_answers_list($id){
        $user = User::find($id);
        if(!empty($user->splash_que_ans)){
            $ssq_user_ans = explode(',',$user->splash_que_ans);
        }else{$ssq_user_ans[] = '';}
        $ssq = SplashScreenQuestions::whereIn('id',$ssq_user_ans)->get();
        return view('Admin.splash-screen-questions.ssq-view-user-answers',compact('ssq'));
    }
}
