<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Http\Requests\MobileApp\CustomTaskCreate;
use App\Models\Admin\PointCalculations;
use App\Models\Admin\Slogan;
use App\Models\Admin\TaskCategory;
use App\Models\Admin\TaskSubCategory;
use App\Models\MobileApp\AddMembers;
use App\Models\MobileApp\AssignTasksToMembers;
use App\Models\MobileApp\MilestonePointsCalculations;
use App\Models\MobileApp\Notification;
use App\Models\MobileApp\Tasks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TasksController extends Controller
{
    public function custom_task_create_view(Request $request){
        $request->validate(['user_id'=>'required']);
        $cat = TaskCategory::all();
        foreach($cat as $category){
            $sub_cat = TaskSubCategory::select('id','user_id','task_sub_category_name','task_sub_category_image')
            ->whereIn('user_id',[0,$request->user_id])
            ->where('task_category_name',$category->id)->get();
            $data[] = ['category' => ['id'=>$category->id,'category_name' => $category->task_category_name],'subcatgories' => $sub_cat];
        }
        return response()->json(['status' => 200,'data' => $data,'message' => 'Catergorywise Sub-Category Data Fetched Successfully']);
    }

    public function custom_task_create(CustomTaskCreate $request){
        $request->validated();
      	if($request->assign_task_type == 'single-day'){
            if($request->time_flag == 'current'){
                $new_wake_up_time = $request->current_time;
            }else{
                $new_wake_up_time = $request->wake_up_time;
            }
        }else{
            $new_wake_up_time = $request->wake_up_time;
        }
        foreach($request->task_name as $task){
            $task_data = TaskSubCategory::find($task);
            $tasks[] = "id ".$task_data->id.", ".$task_data->task_sub_category_name;
        }

      	$task_check = Tasks::where(['user_id' => $request->user_id,'date' => $request->date])->get();
        if($task_check->isNotEmpty()){
            foreach($task_check as $old_task){
                $old_task_data = TaskSubCategory::find($old_task->task_name);
                $old_tasks[] = "id ".$old_task_data->id." ".$old_task_data->task_sub_category_name. " start_time ".$old_task->task_start_time." end_time ".$old_task->task_end_time;
            }
            $str = "You are a planner, and your task is to plan a routine for the given tasks. The details of the tasks, such as ID, task name, start time, and end time of some tasks, are already provided. You need to fill in the missing timing for the tasks that don't have it, following the mentioned rules. The data for the tasks are as follows: - ".implode(', ',$tasks).", ".implode(', ',$old_tasks)." The wake time is ".$new_wake_up_time.", and the sleep time is ".$request->sleep_time.". You must not repeat any tasks, and the timings of the tasks should not overlap. There should be a minimum gap of 5 minutes between the end time of one task and the start time of the next task. Do not include any other tasks or chores other than the ones provided. Plan the tasks with start and end times in the format HH:MM AM/PM. Ensure that the new tasks fit within the specified time frames without overlapping or repeating. Output the schedule in JSON format. The parameters in the JSON objects must include id, task_name, startTime, and endTime. Provide the output as an array without the JSON object.Make sure start time and end time of one any task does not overlap or falls inside the other tasks start and end time ";
        }else{
            $str = "You are a planner, your task is to plan a routine of given tasks, the details of the tasks such as id, task name are as follows ".implode(',',$tasks).", plan the provided tasks with start and end time. the wake time is ".$new_wake_up_time." and sleep time is ".$request->sleep_time.". You do not repeat any tasks and also task timings or you do not keep same timing of tasks and the gap between two tasks should be minimum 5 minutes. you should not add or include any other task or chores other than the tasks provided to you. give the output in JSON format and parameter must be id, task_name, startTime, endTime and do not include the object and show the array.";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $postdata = array("model" => "gpt-3.5-turbo-instruct","prompt" => $str,"temperature" => 0.4,"max_tokens" => 1500,"top_p" => 1,"frequency_penalty" => 0,"presence_penalty" => 0);
        $postdata = json_encode($postdata);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        //$headers[] = 'Authorization: Bearer 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';
      	$headers[] = 'Authorization: Bearer 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';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {echo 'Error:' . curl_error($ch);}
        curl_close($ch);
        $result_new = json_decode($result,true);

        if(empty($result_new['choices'][0]['text'])){
            return response()->json(['status' => 500,'data' => [],'message' => 'Something went wrong please try again']);
        }else{
            $result1 = json_decode($result_new['choices'][0]['text']);
            if($result1 == null){
                return response()->json(['status' => 500,'data' => [],'message' => 'Something went wrong please try again']);
            }else{
                if($request->assign_task_type == 'single-day'){
                    $skipIteration = true;
                    foreach($request->task_name as $user_tasks){
                        foreach($result1 as $rs){
                            if(isset($rs->id)){
                                if($user_tasks == $rs->id){
                                    $existsTasksDataCount = Tasks::where(['user_id'=>$request->user_id,'date'=>$request->date,'task_name'=>$user_tasks])->count();
                                    if($existsTasksDataCount < 3){
                                        $start_change_time = null;
                                        $end_change_time= null;
                                        if(empty($rs->startTime)){ $start_change_time = '00:00 PM'; }else{ $start_change_time = date('g:i A', strtotime($rs->startTime));
                                        }
                                        if(empty($rs->endTime)){ $end_change_time = '00:00 PM';
                                        }else{
                                            if($skipIteration){
                                                $end_change_time1 = date('g:i A', strtotime($rs->endTime));
                                                $start_timestamp = strtotime($start_change_time);
                                                $end_timestamp = strtotime($end_change_time1);
                                                $time_diff = $end_timestamp - $start_timestamp;
                                                $time_diff_minutes = intval($time_diff / 60);

                                                if ($time_diff_minutes < 30) {
                                                    $if_count = true;
                                                    $end_change_time = date('g:i A', strtotime('+30 minutes', strtotime($start_change_time)));
                                                }else{
                                                    $if_count = false;
                                                    $end_change_time = date('g:i A', strtotime($rs->endTime));
                                                }
                                                $skipIteration = false;
                                            }else{
                                                $existsTasksValues = Tasks::orderBy('id','desc')->where(['user_id'=>$request->user_id,'date'=>$request->date])->first();
                                                $start_change_time = date('g:i A', strtotime('+5 minutes', strtotime($existsTasksValues->task_end_time)));
                                                $end_change_time1 = date('g:i A', strtotime($rs->endTime));
                                                $start_timestamp = strtotime($start_change_time);
                                                $end_timestamp = strtotime($end_change_time1);
                                                $time_diff = $end_timestamp - $start_timestamp;
                                                $time_diff_minutes = $time_diff / 60;
                                                if ($time_diff_minutes < 30) {
                                                    $end_change_time = date('g:i A', strtotime('+30 minutes', strtotime($start_change_time)));
                                                }else{
                                                    $end_change_time = date('g:i A', strtotime($rs->endTime));
                                                }
                                            }
                                        }
                                        $request->merge([
                                            'day' => date('D', strtotime($request->date)),
                                            'task_name' => $user_tasks,
                                            'task_start_time' => $start_change_time,
                                            'task_end_time' => $end_change_time,
                                            'task_status'=> 0
                                        ]);
                                        $task_sub_category = TaskSubCategory::find($user_tasks);
                                        $request->merge(['task_assign_status' => 'normal']);
                                        $task_save = Tasks::create($request->all());
                                        $task_ai_data[] = [
                                            'task_main_id' => $task_save->id,
                                            'task_date' => $request->date,
                                            'task_id' => $task_save->task_name,
                                            'task_name' => $task_sub_category->task_sub_category_name,
                                            'start_time' => $task_save->task_start_time,
                                            'end_time' => $task_save->task_end_time,
                                            'rrrrrrrrr' => $time_diff_minutes,
                                            'eeeeeeeee' => $if_count,
                                        ];
                                    }else{
                                        $existsTasksData = Tasks::where(['user_id'=>$request->user_id,'date'=>$request->date,'task_name'=>$user_tasks])->orderBy('id','desc')->first();
                                        $task_sub_category = TaskSubCategory::find($user_tasks);
                                        $task_ai_data = [];
                                        $existingTaskMainIds = array_column($task_ai_data, 'task_main_id');
                                        if (!in_array($existsTasksData->id, $existingTaskMainIds)) {
                                            $task_ai_data[] = [
                                                'task_main_id' => $existsTasksData->id,
                                                'task_date' => $existsTasksData->date,
                                                'task_id' => $existsTasksData->task_name,
                                                'task_name' => $task_sub_category->task_sub_category_name,
                                                'start_time' => $existsTasksData->task_start_time,
                                                'end_time' => $existsTasksData->task_end_time,
                                            ];
                                        }
                                    }
                              	}
                            }else{
                              	return response()->json(['status' => 500,'data' => [],'message' => 'Something went wrong please try again']);
                            }
                        }
                    }
                }else{
                    foreach($request->date as $dates){
                        foreach($request->task_name as $user_tasks){
                            foreach($result1 as $rs){
                                if(isset($rs->id)){
                                  	if($user_tasks == $rs->id){
                                        $existsTasksDataCount = Tasks::where(['user_id'=>$request->user_id,'date'=>$dates,'task_name'=>$user_tasks])->count();
                                        if($existsTasksDataCount < 3){
                                            $start_change_time = null;
                                            $end_change_time= null;
                                            if(empty($rs->startTime)){ $start_change_time = '00:00 PM'; }else{ $start_change_time = date('g:i A', strtotime($rs->startTime)); }
                                            if(empty($rs->endTime)){ $end_change_time = '00:00 PM';
                                            }else{ $end_change_time = date('g:i A', strtotime($rs->endTime)); }
                                            $task_save = Tasks::create([
                                                'user_id' => $request->user_id,
                                                'date' => $dates,
                                                'assign_task_type' => $request->assign_task_type,
                                                'day' => date('D', strtotime($dates)),
                                                'wake_up_time' => $request->wake_up_time,
                                                'sleep_time' => $request->sleep_time,
                                                'task_name' => $user_tasks,
                                                'task_start_time' => $start_change_time,
                                                'task_end_time' => $end_change_time,
                                                'task_assign_status' => 'normal',
                                                'task_status'=> 0
                                            ]);
                                            $task_sub_category = TaskSubCategory::find($user_tasks);
                                            $task_ai_data[] = [
                                                'task_main_id' => $task_save->id,
                                                'task_date' => $task_save->date,
                                                'task_id' => $task_save->task_name,
                                                'task_name' => $task_sub_category->task_sub_category_name,
                                                'start_time' => $task_save->task_start_time,
                                                'end_time' => $task_save->task_end_time,
                                            ];
                                        }else{
                                            $existsTasksData = Tasks::where(['user_id'=>$request->user_id,'date'=>$dates,'task_name'=>$user_tasks])->orderBy('id','desc')->first();
                                            $task_sub_category = TaskSubCategory::find($user_tasks);
                                            $task_ai_data = [];
                                            $existingTaskMainIds = array_column($task_ai_data, 'task_main_id');
                                            if (!in_array($existsTasksData->id, $existingTaskMainIds)) {
                                                $task_ai_data[] = [
                                                    'task_main_id' => $existsTasksData->id,
                                                    'task_date' => $existsTasksData->date,
                                                    'task_id' => $existsTasksData->task_name,
                                                    'task_name' => $task_sub_category->task_sub_category_name,
                                                    'start_time' => $existsTasksData->task_start_time,
                                                    'end_time' => $existsTasksData->task_end_time,
                                                ];
                                            }
                                        }
                                    }
                                }else{
                                  	return response()->json(['status' => 500,'data' => [],'message' => 'Something went wrong please try again']);
                                }
                            }
                        }
                    }
                }
                return response()->json(['status' => 200,'data' => ['ai_tasks' => $task_ai_data],'message' => 'Task Created Successfully']);
            }
        }
    }

    public function task_overlapping_cancel(Request $request){
        if(!empty($request->task_ids)){
            foreach($request->task_ids as $data){
                $tasks = Tasks::find($data);
                if($tasks){ $tasks->delete(); }
            }
            return response()->json(['data'=>[],'message'=>'Tasks Deleted Successfully']);
        }else{
            return response()->json(['data'=>[],'message'=>'Something went wrong']);
        }
    }

    public function reset_tasks(CustomTaskCreate $request){
        $request->validated();
      	if($request->assign_task_type == 'single-day'){
            if($request->time_flag == 'current'){
                $new_wake_up_time = $request->current_time;
            }else{
                $new_wake_up_time = $request->wake_up_time;
            }
        }else{
            $new_wake_up_time = $request->wake_up_time;
        }
        foreach($request->task_name as $task){
            $task_data = TaskSubCategory::find($task);
            $tasks[] = "id ".$task_data->id.", ".$task_data->task_sub_category_name;
        }
        foreach($request->task_name as $task){
            $task_data = TaskSubCategory::find($task);
            $tasks[] = "id ".$task_data->id." ".$task_data->task_sub_category_name;
        }
        $ch = curl_init();
        $str = "You are a planner, your task is to plan a routine of given tasks, the details of the tasks such as id, task name are as follows ".implode(',',$tasks).", plan the provided tasks with start and end time. the wake time is ".$new_wake_up_time." and sleep time is ".$request->sleep_time.". You do not repeat any tasks and also task timings or you do not keep same timing of tasks and the gap between two tasks should be minimum 5 minutes. you should not add or include any other task or chores other than the tasks provided to you. give the output in JSON format and parameter must be id, task_name, startTime, endTime and do not include the object and show the array.";
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $postdata = array("model" => "text-davinci-003","prompt" => $str,"temperature" => 0.4,"max_tokens" => 1500,"top_p" => 1,"frequency_penalty" => 0,"presence_penalty" => 0);
        $postdata = json_encode($postdata);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        //$headers[] = 'Authorization: Bearer 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';
      	$headers[] = 'Authorization: Bearer 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';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {echo 'Error:' . curl_error($ch);}
        curl_close($ch);
        $result_new = json_decode($result,true);
        $result1 = json_decode($result_new['choices'][0]['text']);
        if(empty($result_new['choices'][0]['text'])){
            return response()->json(['status' => 500,'data' => [],'message' => 'Something went wrong please try again']);
        }else{
            $result1 = json_decode($result_new['choices'][0]['text']);
            if($result1 == null){
                return response()->json(['status' => 500,'data' => [],'message' => 'Something went wrong please try again']);
            }else{
                if($request->assign_task_type == 'single-day'){
                    Tasks::where(['user_id'=>$request->user_id,'date'=>$request->date])->delete();
                    foreach($request->task_name as $user_tasks){
                        foreach($result1 as $rs){
                            if(isset($rs->id)){
                              	if($user_tasks == $rs->id){
                                    $start_change_time = null;
                                    $end_change_time= null;
                                    if(empty($rs->startTime)){ $start_change_time = '00:00 PM'; }else{ $start_change_time = date('g:i A', strtotime($rs->startTime)); }
                                    if(empty($rs->endTime)){ $end_change_time = '00:00 PM';
                                    }else{ $end_change_time = date('g:i A', strtotime($rs->endTime)); }
                                    $request->merge([
                                        'day' => date('D', strtotime($request->date)),
                                        'task_name' => $user_tasks,
                                        'task_start_time' => $start_change_time,
                                        'task_end_time' => $end_change_time,
                                        'task_status'=> 0
                                    ]);
                                    $task_sub_category = TaskSubCategory::find($user_tasks);
                                    $request->merge(['task_assign_status' => 'normal']);
                                    $task_save = Tasks::create($request->all());
                                    $task_ai_data[] = [
                                        'task_date' => $request->date,
                                        'task_id' => $task_save->task_name,
                                        'task_name' => $task_sub_category->task_sub_category_name,
                                        'start_time' => $task_save->task_start_time,
                                        'end_time' => $task_save->task_end_time,
                                    ];
                                }
                            }else{
                              	return response()->json(['status' => 500,'data' => [],'message' => 'Something went wrong please try again']);
                            }
                        }
                    }
                }else{
                    foreach($request->date as $remove_date){
                        Tasks::where(['user_id'=>$request->user_id,'date'=>$remove_date])->delete();
                    }
                    foreach($request->date as $dates){
                        foreach($request->task_name as $user_tasks){
                            foreach($result1 as $rs){
                                if(isset($rs->id)){
                                  	if($user_tasks == $rs->id){
                                        $start_change_time = null;
                                        $end_change_time= null;
                                        if(empty($rs->startTime)){ $start_change_time = '00:00 PM'; }else{ $start_change_time = date('g:i A', strtotime($rs->startTime)); }
                                        if(empty($rs->endTime)){ $end_change_time = '00:00 PM';
                                        }else{ $end_change_time = date('g:i A', strtotime($rs->endTime)); }
                                        $task_save = Tasks::create([
                                            'user_id' => $request->user_id,
                                            'date' => $dates,
                                            'assign_task_type' => $request->assign_task_type,
                                            'day' => date('D', strtotime($dates)),
                                            'wake_up_time' => $request->wake_up_time,
                                            'sleep_time' => $request->sleep_time,
                                            'task_name' => $user_tasks,
                                            'task_start_time' => $start_change_time,
                                            'task_end_time' => $end_change_time,
                                            'task_assign_status' => 'normal',
                                            'task_status'=> 0
                                        ]);
                                        $task_sub_category = TaskSubCategory::find($user_tasks);
                                        $task_ai_data[] = [
                                            'task_date' => $task_save->date,
                                            'task_id' => $task_save->task_name,
                                            'task_name' => $task_sub_category->task_sub_category_name,
                                            'start_time' => $task_save->task_start_time,
                                            'end_time' => $task_save->task_end_time,
                                        ];
                                    }
                                }else{
                                  	return response()->json(['status' => 500,'data' => [],'message' => 'Something went wrong please try again']);
                                }
                            }
                        }
                    }
                }
                return response()->json(['status' => 200,'data' => ['ai_tasks' => $task_ai_data],'message' => 'Task Created Successfully']);
            }
        }
    }

    public function task_remove(Request $request){
        $request->validate(['task_id'=>'required']);
        Tasks::find($request->task_id)->delete();
        return response()->json(['status'=>200,'data'=>[],'message'=>'Task Removed']);
    }

    public function task_activities(Request $request){
        $request->validate(['user_id'=>'required','date'=>'required']);
        $tasks_data = Tasks::join('task_sub_categories','task_sub_categories.id','=','tasks.task_name')
        ->select('tasks.id','tasks.task_name','tasks.user_id','tasks.date','task_sub_categories.task_sub_category_name','task_sub_categories.task_sub_category_image','tasks.task_start_time','tasks.task_end_time','tasks.task_status','tasks.event_id','tasks.wake_up_time','tasks.sleep_time')
        ->where(['tasks.user_id' => $request->user_id,'tasks.date' => $request->date])->get();
        if($tasks_data->isNotEmpty()){
            foreach($tasks_data as $task){
                $assign_task_to_member = AssignTasksToMembers::join('add_members','add_members.id','member_id')
                ->where('assign_tasks_to_members.task_id',$task->id)->first();
                $tasks[] = [
                    'task_id' => $task->id,
                    'task_name_id' => $task->task_name,
                    'wake_up_time' => $task->wake_up_time,
                    'sleep_time' => $task->sleep_time,
                    'user_id' => $task->user_id,
                    'task_date' => $task->date,
                    'task_sub_category_name' => $task->task_sub_category_name,
                    'task_sub_category_image' => $task->task_sub_category_image,
                    'task_start_time' => $task->task_start_time,
                    'task_end_time' => $task->task_end_time,
                    'task_status' => $task->task_status == "0" ? 0 : 1,
                    'task_assigned_member_name' => $assign_task_to_member->member_name ?? '',
                    'event_id' => $task->event_id ?? ''
                ];
            }
            return response()->json(['status' => 200,'data' => ['tasks_activities_list'=>$tasks,'tasks_tracking_list'=>$tasks],'message' => 'Activities & Tracking List Fetched Successfully']);
        }else{
            return response()->json(['status' => 500,'data' => [],'message'=>'Data Not Found']);
        }
    }

    public function task_activities_time_update(Request $request){
        $request->validate([
            'user_id' => 'required',
            'task_id' => 'required',
            'task_start_time' => 'required',
            'task_end_time' => 'required',
        ]);
        $task = Tasks::where(['id'=>$request->task_id,'user_id'=>$request->user_id])->first();
        if($task){
            $assigned_task_data = Tasks::find($task->task_assign_id);
          	$assigned_task_data->update(['task_start_time'=>$request->task_start_time,'task_end_time'=>$request->task_end_time]);
          	$task->update(['task_start_time'=>$request->task_start_time,'task_end_time'=>$request->task_end_time]);
            if(isset($request->isReschedule) && $request->isReschedule == 1){
                $task->update(['task_status'=>3]);
            }
            return response()->json(['status' => 200,'data' => [],'message' => 'Task Time Updated Successfully']);
        }else{return response()->json(['status' => 500,'data' => [],'message' => 'Record Not Found']);}
    }

    public function tracking_task_status_update(Request $request){
        $request->validate(['user_id' => 'required','task_id' => 'required','task_status'=>'required']);
        $task = Tasks::where(['id'=>$request->task_id,'user_id'=>$request->user_id])->first();
        if($task){
            if($task->task_assign_status == 'assigned'){
                Tasks::find($task->task_assign_id)->update(['task_status'=>$request->task_status]);
                $taskAssignData = Tasks::find($task->task_assign_id);
                if($taskAssignData && $taskAssignData->task_status == 1){
                    $notificationData = Notification::where(['task_id'=>$task->task_assign_id,'user_id'=>$request->user_id])->first();
                    $userForNotification = User::find($request->user_id);
                    $taskNameForNotification = TaskSubCategory::find($task->task_name);
                    Notification::create([
                        'user_id' => $notificationData->user_id,
                        'assigned_user_id' => $notificationData->assigned_user_id,
                        'member_id' => $notificationData->member_id,
                        'task_id' => $notificationData->task_id,
                        'task_title' => 'Assigned Task Completed Notification',
                        'task_desc' => ucfirst(strtolower($userForNotification->first_name.' '.$userForNotification->last_name." completed your assigned ".$taskNameForNotification->task_sub_category_name.' task.')),
                        'notification_type' => 'task_assign',
                        'task_time' => $request->task_date_time,
                        'notification_status' => 'completed',
                    ]);
                }
            }
            $task->update(['task_status'=>$request->task_status]);

            $points = PointCalculations::first();
            // ======= Task Completion =======

            $category_completed_tasks = Tasks::where(['user_id' => $request->user_id,'task_status' => 1])->count();
            if($category_completed_tasks != 0){
                $task_completion_flag = 1;
            }else{
                $task_completion_flag = 0;
            }

            // ======= End Task Completion =======

            // ======= Max Streak =======
            $user = User::find($request->user_id);
            $yesterdays_tasks = Tasks::where(['user_id' => $request->user_id,'date' => date('Y-m-d',strtotime($request->date.'-1 days')),'task_status' => 1])->count();
            if($yesterdays_tasks > 0){
                if(date('Y-m-d',strtotime($user->updated_at)) != $request->max_streak_date){
                    $user->update(['max_streak' => ($user->max_streak+1)]);
                }
            }else{$user->update(['max_streak' => 0]);}

            if($user->max_streak != 0){$max_streak_flag = 1;}else{$max_streak_flag = 0;};
            // ======= End Max Streak =======

            // ======= Milestones Achievements =======
            $milestone_value = Tasks::where(['user_id' => $request->user_id,'task_status' => 1])->count();
            if($milestone_value != 0){
                $milestone_data = MilestonePointsCalculations::all();
                $milestone_end_point = null;
                if($milestone_data->isNotEmpty()){
                    foreach($milestone_data as $data){
                        if($milestone_value >= $data->milestone_start && $milestone_value <= $data->milestone_end){
                            $milestone_end_point = $data->milestone_end;
                            break;
                        }
                    }
                }
            }
            if($milestone_end_point == $milestone_value){
                $milestone_api_data = [
                    'milestone_achievement_flag' => 1,
                    'milestone_achievement_range' => $milestone_end_point,
                ];
            }else{
                $milestone_api_data = [
                    'milestone_achievement_flag' => 0,
                ];
            }
            // ======= End Milestones Achievements =======

            // ======= Category Completion =======
            $tasks = Tasks::where(['user_id' => $request->user_id,'date' => $request->date])->get();
            foreach($tasks as $data){$task_sub_categories[] = TaskSubCategory::find($data->task_name);}
            if(!empty($task_sub_categories)){
                $uniqueCategoryNames = collect($task_sub_categories)->unique('task_category_name');
                $single_tasks = [];
                $cc_results = [];

                foreach ($uniqueCategoryNames as $value) {
                    $category_name = $value->task_category_name;
                    $categories_wise_data = [
                        $category_name => Tasks::join('task_sub_categories', 'tasks.task_name', '=', 'task_sub_categories.id')
                        ->join('task_categories', 'task_sub_categories.task_category_name', '=', 'task_categories.id')
                        ->where('tasks.user_id', $request->user_id)
                        ->where('task_categories.id', $category_name)
                        ->where('tasks.date', $request->date)
                        ->count(),
                    ];
                    $categories_wise_data_completed = [
                        $category_name => Tasks::join('task_sub_categories', 'tasks.task_name', '=', 'task_sub_categories.id')
                        ->join('task_categories', 'task_sub_categories.task_category_name', '=', 'task_categories.id')
                        ->where('tasks.user_id', $request->user_id)
                        ->where('task_categories.id', $category_name)
                        ->where('tasks.task_status', 1)
                        ->where('tasks.date', $request->date)
                        ->count(),
                    ];

                    $single_task = Tasks::join('task_sub_categories', 'tasks.task_name', '=', 'task_sub_categories.id')
                    ->join('task_categories', 'task_sub_categories.task_category_name', '=', 'task_categories.id')
                    ->select('task_categories.id as category_id')
                    ->where('tasks.id', $request->task_id)
                    ->first();

                    $single_tasks[] = $single_task;
                    $cc_result = [];
                    foreach ($categories_wise_data as $key => $value1) {
                        if ($key === $single_task->category_id) {
                            $value2 = $categories_wise_data_completed[$key];
                            $isCompleted = $value1 === $value2;
                            if ($isCompleted !== null) {$cc_result = ['category_id' => $key, 'is_completed' => $isCompleted];}
                        }
                    }
                    if (!empty($cc_result)) {$cc_results = $cc_result;}
                }

                if($cc_results['is_completed'] === true){
                    $category_main_name = TaskCategory::find($cc_results['category_id']);
                    $category_completion_name = $category_main_name->task_category_name;
                    $category_completion_flag = 1;
                }else{
                    $category_completion_name = null;
                    $category_completion_flag = 0;
                }
            }
            // ======= End Category Completion =======

            return response()->json([
                'status' => 200,
                'data' => [
                    'todays_task_completion_point' => $points->task_completion,
                    'todays_task_completion_flag' => $task_completion_flag,
                    'max_streak_flag' => $max_streak_flag,
                    'milestone_achievements' => $milestone_api_data,
                    'category_completion_name' => $category_completion_name,
                    'category_completion_flag' => $category_completion_flag
                ],
                'message' => 'Task Status Updated'
            ]);
        }else{return response()->json(['status' => 500,'data' => [],'message' => 'Record Not Found']);}
    }

    public function tracking_task_weekly_list(Request $request){
        $request->validate(['user_id'=>'required']);
        $tasks_data = Tasks::join('task_sub_categories','task_sub_categories.id','=','tasks.task_name')
        ->select('tasks.id','tasks.user_id','tasks.date','tasks.day','task_sub_categories.task_sub_category_name','tasks.day','tasks.task_status')
        ->where('tasks.user_id',$request->user_id)
        ->whereBetween('date', [Carbon::parse($request->date)->startOfWeek()->format('Y-m-d'), Carbon::parse($request->date)->endOfWeek()->format('Y-m-d')])->orderBy('date','asc')->get();
        foreach($tasks_data as $task){
            $tasks[] = [
                'task_id' => $task->id,
                'user_id' => $task->user_id,
                'task_date' => $task->date,
                'task_day' => $task->day,
                'task_sub_category_name' => $task->task_sub_category_name,
                'task_status' => $task->task_status == "0" ? 0 : 1,
            ];
        }
        return response()->json(['status' => 200,'data' => ['tasks'=>$tasks],'message' => 'Tracking Task List Fetched Successfully']);
    }

    public function task_categories(){
        $task_categories = TaskCategory::where('task_category_status','active')->get();
        return response()->json(['status' => 200,'data' => ['task_categories' => $task_categories],'message' => 'Task Categories Fetched Successfully']);
    }

    public function custom_task_sub_category(Request $request){
        $request->validate([
            'user_id' => 'required',
            'task_sub_category_name' => 'required|unique:task_sub_categories,task_sub_category_name',
            'task_category_name' => 'required',
        ]);
        // $TaskImageName = null;
        // if($request->hasFile('task_sub_category_image')){
        //     $TaskImageName = 'Task_Image_'.rand(1111111111,99999999999).".".$request->file('task_sub_category_image')->getClientOriginalExtension();
        //     $request->file('task_sub_category_image')->move('assets/images/tasks', $TaskImageName);
        // }
        TaskSubCategory::create([
            'user_id'=>$request->user_id,
            'task_category_name'=>$request->task_category_name,
            'task_sub_category_name'=>$request->task_sub_category_name,
            'task_sub_category_slug'=>Str::slug($request->task_sub_category_name),
            'task_sub_category_image'=>$request->task_sub_category_image,
            'task_sub_category_status'=>'active'
        ]);
        return response()->json(['status' => 200,'data' => [],'message' => 'Task Added Successfully']);
    }

    public function home_screen(Request $request){
        $today_date = $request->date;
        $today_day = date('l',strtotime($today_date));
        $tasks_data = Tasks::join('task_sub_categories','task_sub_categories.id','tasks.task_name')
        ->select('tasks.id','tasks.user_id','task_sub_categories.task_sub_category_name','task_sub_categories.task_sub_category_image','tasks.task_start_time','tasks.task_end_time','tasks.task_status')
        ->where(['tasks.user_id' => $request->user_id,'tasks.date' => $today_date])->get();
        $total_tasks = Tasks::where(['tasks.user_id' => $request->user_id,'tasks.date' => $today_date])->count();
        $pending_tasks = Tasks::where(['tasks.user_id' => $request->user_id,'tasks.date' => $today_date,'task_status'=>'0'])->count();
        $completed_tasks = Tasks::where(['tasks.user_id' => $request->user_id,'tasks.date' => $today_date,'task_status'=>'1'])->count();
        if($tasks_data->isNotEmpty()){
            $tasks_data_completed_count = Tasks::where(['user_id' => $request->user_id,'date' => $today_date,'task_status' => 1])->count();
            $slogan_for_crushing = null;
            if($tasks_data_completed_count < 1){
                $slogan_for_crushing = Slogan::inRandomOrder()->first(['slogan_name']);
            }
            foreach($tasks_data as $task){
                $assign_task_to_member = AssignTasksToMembers::join('add_members','add_members.id','member_id')
                ->where('assign_tasks_to_members.task_id',$task->id)->first();
                $tasks[] = [
                    'task_id' => $task->id,
                    'user_id' => $task->user_id,
                    'task_sub_category_name' => $task->task_sub_category_name,
                    'task_sub_category_image' => $task->task_sub_category_image,
                    'task_start_time' => $task->task_start_time,
                    'task_end_time' => $task->task_end_time,
                    'task_status' => $task->task_status == "0" ? 0 : 1,
                    'task_assigned_member_name' => $assign_task_to_member->member_name ?? '',
                ];
                $task_images[] = $task->task_sub_category_image;
            }

            return response()->json([
                'status' => 200,
                'data' => [
                    'todays_date_time' => $today_day.","." ".$today_date,
                    'slogan_for_crushing' => $slogan_for_crushing,
                    'tasks_image' => $task_images,
                    'total_tasks' => $total_tasks,
                    'pending_tasks' => $pending_tasks,
                    'completed_tasks' => $completed_tasks,
                    'completed_task_percentage' => round($completed_tasks/$total_tasks*100)."%",
                    'tasks' => ['text' => $today_day."'s Task",'tasks_data' => $tasks]
                ],
                'message' => 'Data Fetched Successfully'
            ]);
        }else{
            $slogan = Slogan::inRandomOrder()->first(['slogan_name']);
            return response()->json(['status' => 200,'data' => [],'message' => $slogan->slogan_name]);
        }
    }

    public function choose_member_list(Request $request){
        $members = AddMembers::select('id','member_name')->where('user_id',$request->user_id)->get();
        return response()->json([
            'status' => 200,
            'data' => ['members' => $members],
            'message' => 'Members Data Fetched Successfully'
        ]);
    }

    public function assign_tasks_to_members(Request $request){
        $request->validate(['member_id'=>'required','task_id'=>'required']);
        $mem_data = AddMembers::where('id',$request->member_id)->first();
        $user = User::find($request->user_id);
        $task = Tasks::find($request->task_id);
        $task_name = TaskSubCategory::find($task->task_name);
        $member = User::where(['email'=>$mem_data->member_email])->first();
        $url = 'https://fcm.googleapis.com/fcm/send';
        $data = [
            "registration_ids" => [$member->fcm_token ?? ''],
            "notification" => ["title" => 'Task Assignment Notification',"body" => ucfirst(strtolower($user->first_name.' '.$user->last_name." assigned you ".$task_name->task_sub_category_name.' task.'))]
        ];
        $encodedData = json_encode($data);
        $headers = ['Authorization:key=' . env('FCM_SERVER_KEY'),'Content-Type: application/json'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        $result = curl_exec($ch);
        if ($result === FALSE) {die('Curl failed: ' . curl_error($ch));}
        curl_close($ch);
        Notification::create([
            'user_id' => $request->user_id,
            'assigned_user_id' => $member->id,
            'member_id' => $request->member_id,
            'task_id' => $request->task_id,
            'task_title' => 'Task Assignment Notification',
            'task_desc' => ucfirst(strtolower($user->first_name.' '.$user->last_name." assigned you ".$task_name->task_sub_category_name.' task.')),
            'notification_type' => 'task_assign',
            'task_time' => $request->date,
            'notification_status' => 'requested',
        ]);
        return response()->json(['status' => 200,'data' => [],'message' => 'Task Assigned Successfully']);
    }

    public function assign_tasks_to_members_accept(Request $request){
        $request->validate(['task_id'=>'required']);
        AssignTasksToMembers::create(['user_id' => $request->assigned_user_id,'member_id' => $request->member_id,'task_id' => $request->task_id]);
        $user = User::find($request->user_id);
        $assigned_user_data = User::find($request->assigned_user_id);
        $task = Tasks::find($request->task_id);
        $task_name = TaskSubCategory::find($task->task_name);
        $url = 'https://fcm.googleapis.com/fcm/send';
        $data = [
            "registration_ids" => [$assigned_user_data->fcm_token],
            "notification" => ["title" => 'Task Assignment Notification',"body" => ucfirst(strtolower($user->first_name.' '.$user->last_name." accepted your assigned ".$task_name->task_sub_category_name.' task.'))]
        ];
        $encodedData = json_encode($data);
        $headers = ['Authorization:key=' . env('FCM_SERVER_KEY'),'Content-Type: application/json'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        $result = curl_exec($ch);
        if ($result === FALSE) {die('Curl failed: ' . curl_error($ch));}
        curl_close($ch);
        Notification::create([
            'user_id' => $request->user_id,
            'assigned_user_id' => $request->assigned_user_id,
            'member_id' => $request->member_id,
            'task_id' => $request->task_id,
            'task_title' => 'Task Assignment Notification',
            'task_desc' => ucfirst(strtolower($user->first_name.' '.$user->last_name." accepted your assigned ".$task_name->task_sub_category_name.' task.')),
            'notification_type' => 'task_assign',
            'task_time' => $request->date,
            'notification_status' => 'accepted',
        ]);
        Notification::find($request->notification_id)->delete();
        Tasks::create([
            'user_id' => $request->user_id,
            'event_id' => $task->event_id,
            'date' => $task->date,
            'assign_task_type' => $task->assign_task_type,
            'day' => $task->day,
            'wake_up_time' => $task->wake_up_time,
            'sleep_time' => $task->sleep_time,
            'task_name' => $task->task_name,
            'task_start_time' => $task->task_start_time,
            'task_end_time' => $task->task_end_time,
            'task_assign_status' => 'assigned',
            'task_assign_id' => $task->id,
            'task_status' => $task->task_status,
        ]);
        return response()->json(['status' => 200,'data' => [],'message' => 'Assigned Task Accepted']);
    }

    public function assign_tasks_to_members_reject(Request $request){
        $request->validate(['task_id'=>'required']);
        $user = User::find($request->user_id);
        $assigned_user_data = User::find($request->assigned_user_id);
        $task = Tasks::find($request->task_id);
        $task_name = TaskSubCategory::find($task->task_name);
        $url = 'https://fcm.googleapis.com/fcm/send';
        $data = [
            "registration_ids" => [$assigned_user_data->fcm_token],
            "notification" => ["title" => 'Task Assignment Notification',"body" => ucfirst(strtolower($user->first_name.' '.$user->last_name." rejected your assigned ".$task_name->task_sub_category_name.' task.'))]
        ];
        $encodedData = json_encode($data);
        $headers = ['Authorization:key=' . env('FCM_SERVER_KEY'),'Content-Type: application/json'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        $result = curl_exec($ch);
        if ($result === FALSE) {die('Curl failed: ' . curl_error($ch));}
        curl_close($ch);
        Notification::create([
            'user_id' => $request->user_id,
            'assigned_user_id' => $request->assigned_user_id,
            'member_id' => $request->member_id,
            'task_id' => $request->task_id,
            'task_title' => 'Task Assignment Notification',
            'task_desc' => ucfirst(strtolower($user->first_name.' '.$user->last_name." rejected your assigned ".$task_name->task_sub_category_name.' task.')),
            'notification_type' => 'task_assign',
            'task_time' => $request->date,
            'notification_status' => 'rejected',
        ]);
        Notification::find($request->notification_id)->delete();
        return response()->json(['status' => 200,'data' => [],'message' => 'Assigned Task Rejected']);
    }

    public function add_calender_data_store(Request $request){
        $request->validate(['task_id'=>'required','event_id'=>'required']);
        array_map(function ($task_id, $event_id) {
            Tasks::find($task_id)->update(['event_id'=>$event_id]);
        }, $request->task_id, $request->event_id);
        return response()->json([
            'status' => 200,
            'data' => [],
            'message' => 'Data Stored Successfully'
        ]);
    }

    public function custom_task_images_list(){
        $imagePath = public_path('assets/images/custom_tasks_images');
        $imageNames = [];
        $files = File::files($imagePath);
        foreach ($files as $file) {
            $imageNames[] = [
                'image_name' => pathinfo($file)['basename'],
                'image_url' => url('assets/images/custom_tasks_images')."/".pathinfo($file)['basename'],
            ];
        }
        return response()->json(['status' => 200,'data' => $imageNames,'message' => 'Data Fetched Successfully']);
    }
}
