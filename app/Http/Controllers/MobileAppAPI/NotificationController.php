<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\AddMembers;
use App\Models\MobileApp\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function notification_store(Request $request){
        $request->validate(['task_title' => 'required','task_desc' => 'required']);
        Notification::create([
            'user_id' => $request->user_id,
            'task_title' => $request->task_title,
            'task_desc' => ucfirst(strtolower($request->task_desc)),
            //'task_time' => Carbon::now(),
          	'task_time' => $request->notification_date,
          	'notification_status' => 'normal',
        ]);
        return response()->json(['status' => 200,'data' => [],'message' => 'Notification Saved']);
    }

    public function notification_list(Request $request){
        $notifications = Notification::orderBy('id','desc')->get();
        foreach($notifications as $data){
            if(!empty($data->member_id)){
              	if($request->user_id == $data->assigned_user_id && $data->notification_status == 'completed'){
                    $notifications_data[] = [
                        'id' => $data->id,
                        'assigned_user_id' => $data->user_id,
                        'member_id' => $data->member_id,
                        'task_id' => $data->task_id,
                        'task_title' => $data->task_title,
                        'task_desc' => $data->task_desc,
                        'notification_type' => $data->notification_type,
                        'task_time' => Carbon::parse($data->task_time)->diffForHumans(),
                        'notification_status' => $data->notification_status,
                    ];
                }
                if($request->user_id == $data->assigned_user_id && $data->notification_status == 'requested'){
                    if($data->notification_type == 'member_add'){
                        $notifications_data[] = [
                            'id' => $data->id,
                            'assigned_user_id' => $data->user_id,
                            'member_id' => $data->member_id,
                            'title' => $data->task_title,
                            'description' => $data->task_desc,
                            'notification_type' => $data->notification_type,
                            'date_time' => Carbon::parse($data->task_time)->diffForHumans(),
                            'notification_status' => $data->notification_status,
                        ];
                    }else{
                        $notifications_data[] = [
                            'id' => $data->id,
                            'assigned_user_id' => $data->user_id,
                            'member_id' => $data->member_id,
                            'task_id' => $data->task_id,
                            'task_title' => $data->task_title,
                            'task_desc' => $data->task_desc,
                            'notification_type' => $data->notification_type,
                            'task_time' => Carbon::parse($data->task_time)->diffForHumans(),
                            'notification_status' => $data->notification_status,
                        ];
                    }
                }

                if($request->user_id == $data->assigned_user_id && $data->notification_status == 'accepted'){
                    if($data->notification_type == 'member_add'){
                        $notifications_data[] = [
                            'id' => $data->id,
                            'assigned_user_id' => $data->user_id,
                            'member_id' => $data->member_id,
                            'title' => $data->task_title,
                            'description' => $data->task_desc,
                            'notification_type' => $data->notification_type,
                            'date_time' => Carbon::parse($data->task_time)->diffForHumans(),
                            'notification_status' => $data->notification_status,
                        ];
                    }else{
                        $notifications_data[] = [
                            'id' => $data->id,
                            'assigned_user_id' => $data->user_id,
                            'member_id' => $data->member_id,
                            'task_id' => $data->task_id,
                            'task_title' => $data->task_title,
                            'task_desc' => $data->task_desc,
                            'notification_type' => $data->notification_type,
                            'task_time' => Carbon::parse($data->task_time)->diffForHumans(),
                            'notification_status' => $data->notification_status,
                        ];
                    }
                }

                if($request->user_id == $data->assigned_user_id && $data->notification_status == 'rejected'){
                    if($data->notification_type == 'member_add'){
                        $notifications_data[] = [
                            'id' => $data->id,
                            'assigned_user_id' => $data->user_id,
                            'member_id' => $data->member_id,
                            'title' => $data->task_title,
                            'description' => $data->task_desc,
                            'notification_type' => $data->notification_type,
                            'date_time' => Carbon::parse($data->task_time)->diffForHumans(),
                            'notification_status' => $data->notification_status,
                        ];
                    }else{
                        $notifications_data[] = [
                            'id' => $data->id,
                            'assigned_user_id' => $data->user_id,
                            'member_id' => $data->member_id,
                            'task_id' => $data->task_id,
                            'task_title' => $data->task_title,
                            'task_desc' => $data->task_desc,
                            'notification_type' => $data->notification_type,
                            'task_time' => Carbon::parse($data->task_time)->diffForHumans(),
                            'notification_status' => $data->notification_status,
                        ];
                    }
                }
            }

            if($request->user_id == $data->user_id && $data->member_id == null){
                $notifications_data[] = [
                    'id' => $data->id,
                    'task_title' => $data->task_title,
                    'task_desc' => $data->task_desc,
                    'notification_type' => $data->notification_type,
                    'task_time' => Carbon::parse($data->task_time)->diffForHumans(),
                ];
            }
        }
        return response()->json(['status'=>200,'data'=>['notifications'=>$notifications_data ?? ""],'message'=>'Data Fetched Successfully']);
    }
}
