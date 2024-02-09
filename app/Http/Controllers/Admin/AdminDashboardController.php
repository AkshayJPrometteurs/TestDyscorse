<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TaskCategory;
use App\Models\Admin\TaskSubCategory;
use App\Models\MobileApp\Tasks;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function dashboard(){
        $user = User::count();
        $taskCategory = TaskCategory::count();
        $subTaskCategory = TaskSubCategory::count();
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      	$daily_completed_tasks = Tasks::where(['date'=>date('Y-m-d'),'task_status'=>1])->count();
        $countUserData = [];
        foreach ($months as $month) {
            $count = User::whereRaw("MONTHNAME(created_at) = ?", [$month])->count();
            $countUserData[] = $count;
        }
        $questionCounts = DB::table('splash_screen_questions')
        ->select('splash_screen_questions.id as question_id','splash_screen_questions.questions as question_name', DB::raw('COUNT(users.id) as user_count'))
        ->crossJoin('users')
        ->whereRaw("FIND_IN_SET(splash_screen_questions.id, users.splash_que_ans)")
        ->groupBy('splash_screen_questions.id','splash_screen_questions.questions')
        ->orderBy('splash_screen_questions.id','asc')
        ->get();

        foreach ($questionCounts as $question) {
            $data_user_count_ans[] = [
                "y" => $question->user_count,
                "label" => $question->question_name,
            ];
        }
        $user_ans_count_data = json_encode($data_user_count_ans);
        return view('Admin.dashboard',compact('user','taskCategory','subTaskCategory','daily_completed_tasks','countUserData','user_ans_count_data'));
    }
}
