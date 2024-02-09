<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Http\Requests\MobileApp\AddMemberRequest;
use App\Http\Requests\MobileApp\EditProfileRequest;
use App\Models\Admin\PointCalculations;
use App\Models\Admin\TaskSubCategory;
use App\Models\MobileApp\AddMembers;
use App\Models\MobileApp\AppFeedback;
use App\Models\MobileApp\AppShare;
use App\Models\MobileApp\AssignTasksToMembers;
use App\Models\MobileApp\FriendsFollowers;
use App\Models\MobileApp\FriendsFollowing;
use App\Models\MobileApp\Journal;
use App\Models\MobileApp\LookingFor;
use App\Models\MobileApp\MilestonePointsCalculations;
use App\Models\MobileApp\Notification;
use App\Models\MobileApp\Tasks;
use App\Models\MobileApp\UserBlockOrUnblock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
  	public function edit_profile(EditProfileRequest $request){
        $validated = $request->validated();
        $user = User::find($request->user_id);
        $profileName = $user->profile_image;
        if($request->profile_image_flag == 1){
            if(!empty($profileName) && $profileName[0] == 'P'){
                if(File::exists(public_path()."/assets/images/profile/".$user->profile_image)){
                    File::delete(public_path()."/assets/images/profile/".$user->profile_image);
                }
            }
            User::find($request->user_id)->update(['profile_image' => $request->profile_image]);
        }else{
            if($request->hasFile('profile_image')){
                if(!empty($profileName) && $profileName[0] == 'P'){
                    if(File::exists(public_path()."/assets/images/profile/".$user->profile_image)){
                        File::delete(public_path()."/assets/images/profile/".$user->profile_image);
                    }
                }
                $profileName = 'Profile_'.rand(1111111111,99999999999).".".$request->file('profile_image')->getClientOriginalExtension();
                $request->file('profile_image')->move('assets/images/profile', $profileName);
            }
        }
        $validated['profile_image'] = $profileName;
        $user->update($validated);
        if($request->looking_for){
            LookingFor::where('user_id',$request->user_id)->delete();
            foreach($request->looking_for as $lf){
                LookingFor::create([
                    'user_id' => $request->user_id,
                    'looking_for_title' => $lf,
                    'looking_for_slug' => Str::slug($lf),
                    'looking_for_status' => "active",
                ]);
            }
        }else{
          	LookingFor::where('user_id',$request->user_id)->delete();
        }
        return response()->json(['status' => 200,'data' => [],'message' => 'Profile Updated Successfully']);
    }

  	public function remove_profile_image(Request $request){
        $user = User::find($request->user_id);
        if(File::exists(public_path()."/assets/images/profile/".$user->profile_image)){
            File::delete(public_path()."/assets/images/profile/".$user->profile_image);
        }
        $user->update(['profile_image' => null]);
        return response()->json(['status' => 200,'data' => [],'message' => 'Profile Image Removed']);
    }

    public function add_members(AddMemberRequest $request){
        $request->validated();
        $user_check = User::where('email',$request->member_email)->first();
        if($user_check){
            $add_member_check = AddMembers::where('user_id', $request->user_id)->count();
            if($add_member_check == 3){
                return response()->json(['status' => 200,'data' => [],'message' => 'You are already 3 members added']);
            }else{
                $main_user = User::find($request->user_id);
                Notification::create([
                    'user_id' => $request->user_id,
                    'assigned_user_id' => $user_check->id,
                    'member_id' => $user_check->id,
                    'task_title' => 'Member Add Notification',
                    'task_desc' => ucfirst(strtolower($main_user->first_name.' '.$main_user->last_name." added you as family member.")),
                    'notification_type' => 'member_add',
                    'task_time' => date('Y-m-d H:i:s'),
                    'notification_status' => 'requested',
                    'user_temp_mobile' => $request->member_mobile,
                ]);
                return response()->json(['status' => 200,'data' => [],'message' => 'Member Added Successfully']);
            }
        }else{
            return response()->json(['status' => 200,'data' => [],'message' => 'User Not Registered']);
        }
    }

    public function members_accept(Request $request){
        $request->validate(['notification_id'=>'required']);
        $notification = Notification::find($request->notification_id);
        $user = User::find($notification->member_id);
        AddMembers::create([
            'user_id' => $notification->user_id,
            'member_name' => $user->first_name." ".$user->last_name,
            'member_email' => $user->email,
            'member_mobile' => $notification->user_temp_mobile,
        ]);
        Notification::create([
            'user_id' => $notification->member_id,
            'assigned_user_id' => $notification->user_id,
            'member_id' => $notification->user_id,
            'task_title' => 'Member Add Notification',
            'task_desc' => ucfirst(strtolower($user->first_name.' '.$user->last_name." accepted your family member added request.")),
            'notification_type' => 'member_add',
            'task_time' => $request->date,
            'notification_status' => 'accepted',
        ]);
        $notification->delete();
        return response()->json(['status' => 200,'data' => [],'message' => 'User Accepted Your Member Added Request ']);
    }

    public function members_reject(Request $request){
        $request->validate(['notification_id'=>'required']);
        $notification = Notification::find($request->notification_id);
        $user = User::find($notification->member_id);
        Notification::create([
            'user_id' => $notification->member_id,
            'assigned_user_id' => $notification->user_id,
            'member_id' => $notification->user_id,
            'task_title' => 'Member Add Notification',
            'task_desc' => ucfirst(strtolower($user->first_name.' '.$user->last_name." rejected your family member added request.")),
            'notification_type' => 'member_add',
            'task_time' => $request->date,
            'notification_status' => 'rejected',
        ]);
        $notification->delete();
        return response()->json(['status' => 200,'data' => [],'message' => 'User Rejected Your Member Added Request']);
    }

    public function friend_suggestion(Request $request){
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $userData = User::select('users.id','users.first_name','users.last_name','users.profile_image')
        ->whereNotIn('users.id', $blockedUserIDs)
        ->where('users.id','!=',$request->user_id)
        ->orderBy('users.id','desc')
        ->get();
        $following_status = null;
        foreach($userData as $users){
            $user_following = FriendsFollowing::where('user_id',$request->user_id)
            ->where('friend_user_id',$users->id)
            ->first();
            if($user_following){
                if($user_following->following_status == 'approved'){continue;}else{$following_status = 1;}
            }else{
                $friend_following = FriendsFollowing::where('friend_user_id',$request->user_id)
                ->where('user_id',$users->id)
                ->first();
                if($friend_following){
                    if($friend_following->following_status == 'approved'){continue;}else{$following_status = 1;}
                }else{$following_status = 0;}
            }
            $friendSuggestion[] = [
                'id' => $users->id,
                'first_name' => $users->first_name,
                'last_name' => $users->last_name,
                'profile_image' => $users->profile_image,
                'follow_status' =>  $following_status,
            ];
        }
        return response()->json(['status' => 200,'data' => ['friends' => $friendSuggestion],'message' => 'Friends Suggestions List Fetched Successfully']);
    }

    public function friend_suggestion_follow(Request $request){
        $request->validate(['friend_user_id'=>'required']);
        $friend_user = User::find($request->friend_user_id);
        $following_status = null;
        $followers_status = null;
        if($friend_user->privacy == 'Public'){$followers_status = 'approved';$following_status = 'approved';
        }else{$followers_status = 'pending';$following_status = 'requested';}
        FriendsFollowing::create(['user_id' => $request->user_id,'friend_user_id' => $request->friend_user_id,'following_status' => $following_status]);
        FriendsFollowers::create(['user_id' => $request->friend_user_id,'friend_user_id' => $request->user_id,'followers_status' => $followers_status]);
        return response()->json(['status' => 200,'data' => [],'message' => 'Followed Successfully']);
    }

    public function friend_unfollow(Request $request){
        $request->validate(['friend_user_id'=>'required']);
        FriendsFollowing::where(['user_id' => $request->user_id,'friend_user_id' => $request->friend_user_id])->delete();
        FriendsFollowers::where(['user_id' => $request->friend_user_id,'friend_user_id' => $request->user_id])->delete();
        return response()->json(['status' => 200,'data' => [],'message' => 'Unfollow Successfully']);
    }

  	public function following_list(Request $request){
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $following_list = FriendsFollowing::join('users', 'users.id', 'friends_followings.friend_user_id')
        ->select('friends_followings.id as ff_id', 'users.id as user_id', 'users.first_name', 'users.last_name', 'users.profile_image', 'friends_followings.following_status')
        ->where('friends_followings.user_id', $request->user_id)
        ->where('friends_followings.following_status', 'approved')
        ->whereNotIn('users.id', $blockedUserIDs)
        ->get();
        foreach($following_list as $ff){
            $follow_list[] = [
                'id' => $ff->ff_id,
                'user_id' => $ff->user_id,
                'first_name' => $ff->first_name,
                'last_name' => $ff->last_name,
                'profile_image' => $ff->profile_image,
                'follow_status' => 2,
            ];
        }
        return response()->json(['status' => 200,'data'=>['following_list'=>$follow_list ?? ''],'message' => 'User Following List Fetched Successfully']);
    }

    public function followers_list(Request $request){
        $followers_request_pendings = FriendsFollowers::where(['user_id'=>$request->user_id,'followers_status' => 'pending'])->count();
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $follower_list = FriendsFollowers::join('users', 'users.id', 'friends_followers.friend_user_id')
        ->select('friends_followers.id as ff_id', 'users.id as user_id', 'users.first_name', 'users.last_name', 'users.profile_image')
        ->where('friends_followers.user_id', $request->user_id)
        ->where('friends_followers.followers_status', 'approved')
        ->whereNotIn('users.id', $blockedUserIDs)
        ->get();

        foreach($follower_list as $ff){
            $follow_list[] = [
                'id' => $ff->ff_id,
                'user_id' => $ff->user_id,
                'first_name' => $ff->first_name,
                'last_name' => $ff->last_name,
                'profile_image' => $ff->profile_image,
                'follow_status' => 2,
            ];
        }
        return response()->json([
            'status' => 200,
            'data'=>['followers_request_pending' => $followers_request_pendings,'followers_list'=>$follow_list ?? ''],
            'message' => 'User Followers List Fetched Successfully'
        ]);
    }

    public function followers_pending_requests_list(Request $request){
        $follower_pending_list = FriendsFollowers::join('users','users.id','friend_user_id')
        ->select('friends_followers.id as ff_id','users.id as user_id','users.first_name','users.last_name','users.profile_image')
        ->where('friends_followers.user_id', $request->user_id)
        ->where('friends_followers.followers_status', 'pending')->get();
        foreach($follower_pending_list as $ff){
            $follow_list[] = [
                'id' => $ff->ff_id,
                'user_id' => $ff->user_id,
                'first_name' => $ff->first_name,
                'last_name' => $ff->last_name,
                'profile_image' => $ff->profile_image,
                'follow_status' => 1,
            ];
        }
        return response()->json(['status' => 200,'data'=>['followers_pending_list'=>$follow_list ?? ''],'message' => 'User Followers Pending List Fetched Successfully']);
    }

    public function approve_friend_request(Request $request){
        $request->validate(['friend_user_id'=>'required']);
        $following_pending_list = FriendsFollowing::where([
            'user_id' => $request->friend_user_id,
            'friend_user_id' => $request->user_id,
            'following_status' => 'requested'
        ])->first();
        $followers_pending_list = FriendsFollowers::where([
            'user_id' => $request->user_id,
            'friend_user_id' => $request->friend_user_id,
            'followers_status' => 'pending'
        ])->first();
        $following_pending_list->update(['following_status'=>'approved']);
        $followers_pending_list->update(['followers_status'=>'approved']);
        return response()->json(['status' => 200,'data' => [],'message' => 'Friend Request Approved Successfully']);
    }

  	 public function reject_friend_request(Request $request){
        $request->validate(['friend_user_id'=>'required']);
        FriendsFollowing::where(['user_id' => $request->friend_user_id,'friend_user_id' => $request->user_id,'following_status' => 'requested'])->delete();
        FriendsFollowers::where(['user_id' => $request->user_id,'friend_user_id' => $request->friend_user_id,'followers_status' => 'pending'])->delete();
        return response()->json(['status' => 200,'data' => [],'message' => 'Friend Request Rejected Successfully']);
    }

  	public function user_profile(Request $request){
        $user = User::select('id','profile_image','first_name','last_name','about','gender','age','max_streak','privacy','updated_at')->find($request->user_id);
        $yesterdays_tasks = Tasks::where(['user_id' => $request->user_id,'date' => date('Y-m-d',strtotime($request->date.'-1 days')),'task_status' => 1])->count();
        if($yesterdays_tasks > 0){
            if(date('Y-m-d',strtotime($user->updated_at)) != date('Y-m-d',strtotime($request->max_streak_date))){
                $user->update(['max_streak' => ($user->max_streak+1)]);
            }
        }else{$user->update(['max_streak' => 0]);}
        $looking_for = LookingFor::select('looking_for_title')->where('user_id', $request->user_id)->get();
        $members = AddMembers::where('user_id', $request->user_id)->count();
        $followers = FriendsFollowers::where(['user_id' => $request->user_id, 'followers_status' => 'approved'])->count();
        $following = FriendsFollowing::where(['user_id' => $request->user_id, 'following_status' => 'approved'])->count();
        $userData = User::select('id','first_name','last_name','profile_image')->where('id','!=',$request->user_id)->orderBy('id','desc')->get();
        $following_status = null;
     	$friendSuggestion = null;
        foreach($userData as $users){
            $user_following = FriendsFollowing::where('user_id',$request->user_id)
            ->where('friend_user_id',$users->id)
            ->first();
          	$followed_user_name = null;
            $friend_to_friend_follower = FriendsFollowers::where('user_id',$users->id)->get();
            if($friend_to_friend_follower->isNotEmpty()){
                foreach($friend_to_friend_follower as $ftff){
                    $friend_follower = FriendsFollowers::where('user_id',$ftff->user_id)
                    ->where('followers_status','approved')
                    ->first();
                    if($friend_follower){
                        $followed_user = User::where('id',$friend_follower->friend_user_id)->first();
                      	if(isset($followed_user->first_name)){
                          $followed_first_name = $followed_user->first_name;
                        }else{
                          $followed_first_name = '';
                        }
                      	if(isset($followed_user->last_name)){
                          $followed_last_name = $followed_user->last_name;
                        }else{
                          $followed_last_name = '';
                        }
                        $followed_user_name = $followed_first_name." ".$followed_last_name;
                    }
                }
            }
            if($user_following){
                if($user_following->following_status == 'approved'){continue;}else{$following_status = 1; }
            }else{
                $friend_following = FriendsFollowing::where('friend_user_id',$request->user_id)
                ->where('user_id',$users->id)
                ->first();
                if($friend_following){
                    if($friend_following->following_status == 'approved'){continue;}else{$following_status = 1;}
                }else{$following_status = 0;}
            }
            $friendSuggestion[] = [
                'id' => $users->id,
                'first_name' => $users->first_name,
                'last_name' => $users->last_name,
                'profile_image' => $users->profile_image,
                'follow_status' =>  $following_status,
              	'followed_by' => $followed_user_name,
            ];
        }

        /* =========== POINT CALCULATIONS =========== */

        $points = PointCalculations::first();

        // ======= Task Completion =======
        /*$pc_total_task = Tasks::where(['user_id' => $request->user_id,'date' => date('Y-m-d')])->count();
        if($pc_total_task != 0){
            $pc_completed_tasks = Tasks::where(['user_id' => $request->user_id,'date' => date('Y-m-d'),'task_status' => 1])->count();
            $pc_tc_overall = $pc_total_task - $pc_completed_tasks;
            if($pc_tc_overall == 0){$pc_task_completion = $points->task_completion;}else{$pc_task_completion = 0;}
        }else{$pc_task_completion = 0;}*/
      	$category_completed_tasks = Tasks::where(['user_id' => $request->user_id,'task_status' => 1])->count();
        if($category_completed_tasks != 0){
            $pc_task_completion = $category_completed_tasks;
        }else{
            $pc_task_completion = 0;
        }
        // ======= End Task Completion =======

        // ======= Max Streak =======
        if($user->max_streak != 0){$pc_max_streak = $points->max_streak;}else{$pc_max_streak = 0;};
        // ======= End Max Streak =======

        // ======= Milestones Achievements =======
        $milestone_value = Tasks::where(['user_id' => $request->user_id,'task_status' => 1])->count();
        if($milestone_value != 0){
            $milestone_data = MilestonePointsCalculations::all();
            $milestone_data_single = MilestonePointsCalculations::first();
            $pc_milestone = null;

            if($milestone_data->isNotEmpty()){
                foreach($milestone_data as $data){
                    if($data->milestone_end == $milestone_value){ $pc_milestone = $data->milestone_points; break;
                    }else{
                        if($milestone_value <= $milestone_data_single->milestone_end){ $pc_milestone = 0; break;
                        }else if($milestone_value >= $data->milestone_start && $milestone_value <= $data->milestone_end) {
                            $currentRecord = MilestonePointsCalculations::where('milestone_start', '<=', $milestone_value)
                            ->where('milestone_end', '>=', $milestone_value)->first();
                            $previousRecord = MilestonePointsCalculations::where('id', '<', $currentRecord->id)->orderBy('id', 'desc')->first();
                            $pc_milestone = $previousRecord->milestone_points;
                            break;
                        }
                    }
                }
            }
        }else{
            $pc_milestone = 0;
        }

        // ======= End Milestones Achievements =======

        // ======= Social Engagement =======
        $pc_social_engagement_followers = $following * $points->se_follow;
        $pc_social_engagement_assign_tasks = AssignTasksToMembers::where('user_id',$request->user_id)->count() * $points->se_assigning_task_to_family_member;
        // ======= End Social Engagement =======

        // ======= Feedback =======
        $pc_feedback = AppFeedback::where('user_id',$request->user_id)->count() * $points->feedback;
        // ======= End Feedback =======

        // ======= App Sharing =======
        $pc_app_shared_data = AppShare::where('user_id',$request->user_id)->get();
        if($pc_app_shared_data->isNotEmpty()){
            foreach($pc_app_shared_data as $data){$pc_app_shared_array[] = User::where('email', $data->shared_email)->first();}
            $pc_app_shared = count(array_filter($pc_app_shared_array)) * $points->app_sharing;
        }else{$pc_app_shared = 0;}
        // ======= End App Sharing =======

        // ======= Reflection and Review =======
        $pc_journal = Journal::where('user_id',$request->user_id)->count() * $points->reflection_and_review;
        // ======= End Reflection and Review =======

        // ======= Category Completion =======
        $tasks = Tasks::where(['user_id' => $request->user_id,'date' => $request->date])->get();
      	$pc_category_completion = null;
        foreach($tasks as $data){$task_sub_categories[] = TaskSubCategory::find($data->task_name);}
        if(!empty($task_sub_categories)){
            $uniqueCategoryNames = collect($task_sub_categories)->unique('task_category_name');
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

                foreach ($categories_wise_data as $key => $value1) {
                    $value2 = $categories_wise_data_completed[$key];
                    $isCompleted = $value1 === $value2;
                    $cc_result[] = ['category_id' => $key, 'is_completed' => $isCompleted];
                }
            }
            $atLeastOneCompleted = false;
            $allCompleted = true;
            foreach ($cc_result as $item) {if ($item['is_completed']) {$atLeastOneCompleted = true;} else {$allCompleted = false;}}
            if ($atLeastOneCompleted) {
                if ($allCompleted) {
                    $pc_category_completion = $points->category_completion;
                } else {
                    $pc_category_completion = $points->category_completion;
                }
            } else {
                $pc_category_completion = 0;
            }
        }else {
            $pc_category_completion = 0;
        }
        // ======= Category Completion =======

        $points = $pc_task_completion + $pc_max_streak + $pc_milestone + $pc_social_engagement_followers + $pc_social_engagement_assign_tasks + $pc_feedback + $pc_app_shared + $pc_journal + $pc_category_completion;

        /*echo "Task Completion : ".$pc_task_completion."\n";
        echo "Max Streak : ".$pc_max_streak."\n";
        echo "Milestone Achivements : ".$pc_milestone."\n";
        echo "Social Engagement 1 : ".$pc_social_engagement_followers."\n";
        echo "Social Engagement 2 : ".$pc_social_engagement_assign_tasks."\n";
        echo "Feedback : ".$pc_feedback."\n";
        echo "App Sharing : ".$pc_app_shared."\n";
        echo "Reflection and Review : ".$pc_journal."\n";
        echo "Category Completion : ".$pc_category_completion."\n";
      	die;*/

        /* =========== END POINT CALCUATIONS =========== */

      	$blocked_user_list = User::join('user_block_or_unblocks','user_block_or_unblocks.friend_user_id','users.id')
        ->select('users.id','users.first_name','users.last_name','users.email','users.profile_image')
        ->where('user_id',$request->user_id)->get();

        return response()->json([
            'status' => 200,
            'data' => [
                'user' => $user,
                'looking_for' => $looking_for,
                'statistics' => [
                    'points' => $points,
                    'members' => $members,
                    'followers' => $followers,
                    'following' => $following,
                ],
              	'block_user_list' => $blocked_user_list,
                'friends_suggestion' => $friendSuggestion
            ]
        ]);
    }

  	public function user_following_search(Request $request){
        $request->validate(['keyword'=>'required']);
        $keyword = $request->keyword;
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $user = FriendsFollowing::join('users','users.id','friend_user_id')
        ->select('users.*','friends_followings.user_id','friends_followings.following_status')
        ->where(['friends_followings.user_id' => $request->user_id,'friends_followings.following_status' => 'approved'])
        ->where(function($query) use ($keyword) {$query->where('users.first_name', 'LIKE', '%' . $keyword . '%')->orWhere('users.last_name', 'LIKE', '%' . $keyword . '%');})
        ->whereNotIn('users.id',$blockedUserIDs)
        ->where('users.id','!=',$request->user_id)->get();
        if($user->isNotEmpty()){
            $follow_status = null;
            foreach ($user as $users) {
                $friendFollowing = FriendsFollowing::join('users','users.id','friend_user_id')
                ->where('friends_followings.user_id', $request->user_id)->first();
                if($friendFollowing){
                    if($friendFollowing->following_status == 'pending'){$follow_status = 1;
                    }else{$follow_status = 2;}
                }else{$follow_status = 0;}
                $user_data[] = [
                    'id' => $users->id,
                    'first_name' => $users->first_name,
                    'last_name' => $users->last_name,
                    'profile_image' => $users->profile_image,
                    'follow_status' => $follow_status
                ];
            }
            return response()->json(['status' => 200,'data' => ['users' => $user_data],'message' => 'Data Fetched Successfully']);
        }else{
            return response()->json(['status' => 200,'data' => [],'message' => 'No Data Found.']);
        }
    }

    public function user_followers_search(Request $request){
        $request->validate(['keyword'=>'required']);
        $keyword = $request->keyword;
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $user = FriendsFollowers::join('users','users.id','friend_user_id')
        ->select('users.*','friends_followers.user_id','friends_followers.followers_status')
        ->where(['friends_followers.user_id' => $request->user_id,'friends_followers.followers_status' => 'approved'])
        ->where(function($query) use ($keyword) {$query->where('users.first_name', 'LIKE', '%' . $keyword . '%')->orWhere('users.last_name', 'LIKE', '%' . $keyword . '%');})
        ->whereNotIn('users.id',$blockedUserIDs)
        ->where('users.id','!=',$request->user_id)->get();
        if($user->isNotEmpty()){
            $follow_status = null;
            foreach ($user as $users) {
                $friendFollowers = FriendsFollowers::join('users','users.id','friend_user_id')
                ->where('friends_followers.user_id', $users->id)->first();
                if($friendFollowers){
                    if($friendFollowers->followers_status == 'pending'){$follow_status = 1;
                    }else{$follow_status = 2;}
                }else{$follow_status = 0;}
                $user_data[] = [
                    'id' => $users->id,
                    'first_name' => $users->first_name,
                    'last_name' => $users->last_name,
                    'profile_image' => $users->profile_image,
                    'follow_status' => $follow_status
                ];
            }
            return response()->json(['status' => 200,'data' => ['users' => $user_data],'message' => 'Data Fetched Successfully']);
        }else{
            return response()->json(['status' => 200,'data' => [],'message' => 'No Data Found.']);
        }
    }

    public function friend_profile(Request $request){
        $user = User::select('id','profile_image','first_name','last_name','about','max_streak','privacy','updated_at')->find($request->friend_user_id);
        $yesterdays_tasks = Tasks::where(['user_id' => $request->user_id,'date' => date('Y-m-d',strtotime($request->date.'-1 days')),'task_status' => 1])->count();
        if($yesterdays_tasks > 0){
            if(date('Y-m-d',strtotime($user->updated_at)) != date('Y-m-d',strtotime($request->max_streak_date))){
                $user->update(['max_streak' => ($user->max_streak+1)]);
            }
        }else{$user->update(['max_streak' => 0]);}
        $looking_for = LookingFor::select('looking_for_title')->where('user_id', $request->friend_user_id)->get();
        $followers = FriendsFollowers::where(['user_id' => $request->friend_user_id, 'followers_status' => 'approved'])->count();
        $following = FriendsFollowing::where(['user_id' => $request->friend_user_id, 'following_status' => 'approved'])->count();
        $following_button_status = null;
        $following_button_check = FriendsFollowing::where(['user_id' => $request->user_id,'friend_user_id' => $request->friend_user_id,])->first();
        if($following_button_check){
            if($following_button_check->following_status == 'approved'){
                $following_button_status = 2;
            }else{
                $following_button_status = 1;
            }
        }else{
            $following_button_status = 0;
        }
        $userData = User::select('id','first_name','last_name','profile_image')->where('id','!=',$request->user_id)->where('id','!=',$request->friend_user_id)->orderBy('id','desc')->get();
        $following_status = null;
      	$friendSuggestion = null;
        foreach($userData as $users){
            $user_following = FriendsFollowing::where('user_id',$request->user_id)
            ->where('friend_user_id',$users->id)
            ->first();
          	$followed_user_name = null;
            $friend_to_friend_follower = FriendsFollowers::where('user_id',$users->id)->get();
            if($friend_to_friend_follower->isNotEmpty()){
                foreach($friend_to_friend_follower as $ftff){
                    $friend_follower = FriendsFollowers::where('user_id',$ftff->user_id)
                    ->where('followers_status','approved')
                    ->first();
                    if($friend_follower){
                        $followed_user = User::where('id',$friend_follower->friend_user_id)->first();
                      	if(isset($followed_user->first_name)){
                          $followed_first_name = $followed_user->first_name;
                        }else{
                          $followed_first_name = '';
                        }
                      	if(isset($followed_user->last_name)){
                          $followed_last_name = $followed_user->last_name;
                        }else{
                          $followed_last_name = '';
                        }
                        $followed_user_name = $followed_first_name." ".$followed_last_name;
                    }
                }
            }
            if($user_following){
                if($user_following->following_status == 'approved'){continue;}else{$following_status = 1;}
            }else{
                $friend_following = FriendsFollowing::where('friend_user_id',$request->user_id)
                ->where('user_id',$users->id)
                ->first();
                if($friend_following){
                    if($friend_following->following_status == 'approved'){continue;}else{$following_status = 1;}
                }else{$following_status = 0;}
            }
            $friendSuggestion[] = [
                'id' => $users->id,
                'first_name' => $users->first_name,
                'last_name' => $users->last_name,
                'profile_image' => $users->profile_image,
                'follow_status' =>  $following_status,
              	'followed_by' => $followed_user_name,
            ];
        }

        /* =========== POINT CALCULATIONS =========== */

        $points = PointCalculations::first();

        // ======= Task Completion =======
        /*$pc_total_task = Tasks::where(['user_id' => $request->friend_user_id,'date' => date('Y-m-d')])->count();
        if($pc_total_task != 0){
            $pc_completed_tasks = Tasks::where(['user_id' => $request->friend_user_id,'date' => date('Y-m-d'),'task_status' => 1])->count();
            $pc_tc_overall = $pc_total_task - $pc_completed_tasks;
            if($pc_tc_overall == 0){$pc_task_completion = $points->task_completion;}else{$pc_task_completion = 0;}
        }else{$pc_task_completion = 0;}*/

      	$category_completed_tasks = Tasks::where(['user_id' => $request->friend_user_id,'task_status' => 1])->count();
        if($category_completed_tasks != 0){
            $pc_task_completion = $category_completed_tasks;
        }else{
            $pc_task_completion = 0;
        }
        // ======= End Task Completion =======

        // ======= Max Streak =======
        if($user->max_streak != 0){$pc_max_streak = $points->max_streak;}else{$pc_max_streak = 0;};
        // ======= End Max Streak =======

        // ======= Milestones Achievements =======
        $milestone_value = Tasks::where(['user_id' => $request->friend_user_id,'task_status' => 1])->count();
        if($milestone_value != 0){
            $milestone_data = MilestonePointsCalculations::all();
            $milestone_data_single = MilestonePointsCalculations::first();
            $pc_milestone = null;

            if($milestone_data->isNotEmpty()){
                foreach($milestone_data as $data){
                    if($data->milestone_end == $milestone_value){ $pc_milestone = $data->milestone_points; break;
                    }else{
                        if($milestone_value <= $milestone_data_single->milestone_end){ $pc_milestone = 0; break;
                        }else if($milestone_value >= $data->milestone_start && $milestone_value <= $data->milestone_end) {
                            $currentRecord = MilestonePointsCalculations::where('milestone_start', '<=', $milestone_value)
                            ->where('milestone_end', '>=', $milestone_value)->first();
                            $previousRecord = MilestonePointsCalculations::where('id', '<', $currentRecord->id)->orderBy('id', 'desc')->first();
                            $pc_milestone = $previousRecord->milestone_points;
                            break;
                        }
                    }
                }
            }
        }else{
            $pc_milestone = 0;
        }
        // ======= End Milestones Achievements =======

        // ======= Social Engagement =======
        $pc_social_engagement_followers = $following * $points->se_follow;
        $pc_social_engagement_assign_tasks = AssignTasksToMembers::where('user_id',$request->friend_user_id)->count() * $points->se_assigning_task_to_family_member;
        // ======= End Social Engagement =======

        // ======= Feedback =======
        $pc_feedback = AppFeedback::where('user_id',$request->friend_user_id)->count() * $points->feedback;
        // ======= End Feedback =======

        // ======= App Sharing =======
        $pc_app_shared_data = AppShare::where('user_id',$request->friend_user_id)->get();
        if($pc_app_shared_data->isNotEmpty()){
            foreach($pc_app_shared_data as $data){$pc_app_shared_array[] = User::where('email', $data->shared_email)->first();}
            $pc_app_shared = count(array_filter($pc_app_shared_array)) * $points->app_sharing;
        }else{$pc_app_shared = 0;}
        // ======= End App Sharing =======

        // ======= Reflection and Review =======
        $pc_journal = Journal::where('user_id',$request->friend_user_id)->count() * $points->reflection_and_review;
        // ======= End Reflection and Review =======

        // ======= Category Completion =======
        $tasks = Tasks::where(['user_id' => $request->friend_user_id,'date' => $request->date])->get();
        foreach($tasks as $data){$task_sub_categories[] = TaskSubCategory::find($data->task_name);}
      	$pc_category_completion = null;
        if(!empty($task_sub_categories)){
            $uniqueCategoryNames = collect($task_sub_categories)->unique('task_category_name');
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

                foreach ($categories_wise_data as $key => $value1) {
                    $value2 = $categories_wise_data_completed[$key];
                    $isCompleted = $value1 === $value2;
                    $cc_result[] = ['category_id' => $key, 'is_completed' => $isCompleted];
                }
            }
            $atLeastOneCompleted = false;
            $allCompleted = true;
            foreach ($cc_result as $item) {if ($item['is_completed']) {$atLeastOneCompleted = true;} else {$allCompleted = false;}}
            if ($atLeastOneCompleted) {
                if ($allCompleted) {
                    $pc_category_completion = $points->category_completion;
                } else {
                    $pc_category_completion = $points->category_completion;
                }
            } else {
                $pc_category_completion = 0;
            }
        }else {
            $pc_category_completion = 0;
        }
        // ======= Category Completion =======

        $points = $pc_task_completion + $pc_max_streak + $pc_milestone + $pc_social_engagement_followers + $pc_social_engagement_assign_tasks + $pc_feedback + $pc_app_shared + $pc_journal + $pc_category_completion;

        // echo "Task Completion : ".$pc_task_completion."\n";
        // echo "Max Streak : ".$pc_max_streak."\n";
        // echo "Milestone Achivements : ".$pc_milestone."\n";
        // echo "Social Engagement 1 : ".$pc_social_engagement_followers."\n";
        // echo "Social Engagement 2 : ".$pc_social_engagement_assign_tasks."\n";
        // echo "Feedback : ".$pc_feedback."\n";
        // echo "App Sharing : ".$pc_app_shared."\n";
        // echo "Reflection and Review : ".$pc_journal."\n";
        // echo "Category Completion : ".$pc_category_completion."\n";

        /* =========== END POINT CALCUATIONS =========== */
        if($user->privacy == 'Public'){
            return response()->json([
                'status' => 200,
                'data' => [
                    'user' => $user,
                    'looking_for' => $looking_for,
                    'following_button_status' => $following_button_status,
                    'max_streak' => '',
                    'statistics' => [
                        'points' => $points,
                        'followers' => $followers,
                        'following' => $following,
                    ],
                    'friends_suggestion' => $friendSuggestion
                ]
            ]);
        }else{
            if($following_button_status == 2){
                $statistics = [
                    'points' => $points,
                    'followers' => $followers,
                    'following' => $following,
                ];
            }else{
                $statistics = [
                    'points' => 0,
                    'followers' => 0,
                    'following' => 0,
                ];
            }
            return response()->json([
                'status' => 200,
                'data' => [
                    'user' => $user,
                    'looking_for' => $looking_for,
                    'following_button_status' => $following_button_status,
                  	'statistics' => $statistics,
                    'friends_suggestion' => $friendSuggestion
                ]
            ]);
        }
    }

  	public function update_user_fcm_token(Request $request){
        $request->validate(['token' => 'required']);
        User::find($request->user_id)->update(['fcm_token' => $request->token]);
        return response()->json(['status'=>200,'data'=>[],'message'=>'FCM Token Updated Successfully']);
    }

  	public function friend_suggestion_search(Request $request){
      	$request->validate(['keyword'=>'required']);
        $keyword = $request->keyword;
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $user = User::where(function($query) use ($keyword) {$query->where('first_name', 'LIKE', '%' . $keyword . '%')->orWhere('last_name', 'LIKE', '%' . $keyword . '%');})
        ->whereNotIn('users.id', $blockedUserIDs)
        ->where('id','!=',$request->user_id)
        ->get();
        if($user->isNotEmpty()){
          	foreach ($user as $users) {
                $friendFollowing = FriendsFollowing::join('users','users.id','friend_user_id')
                ->where(['friends_followings.user_id' => $request->user_id,'friends_followings.friend_user_id' => $users->id])->first();
                if($friendFollowing){
                    if($friendFollowing->following_status == 'pending'){$follow_status = 1;
                    }else{$follow_status = 2;}
                }else{$follow_status = 0;}
                $user_data[] = [
                    'id' => $users->id,
                    'first_name' => $users->first_name,
                    'last_name' => $users->last_name,
                    'profile_image' => $users->profile_image,
                    'follow_status' => $follow_status
                ];
            }
            return response()->json(['status' => 200,'data' => ['users' => $user_data],'message' => 'Data Fetched Successfully']);
        }else{
            return response()->json(['status' => 200,'data' => [],'message' => 'No Data Found.']);
        }
    }

  	public function user_friends_following(Request $request){
        $request->validate(['friend_id'=>'required']);
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $user = FriendsFollowing::join('users','users.id','friend_user_id')
        ->select('users.*','friends_followings.user_id','friends_followings.following_status')
        ->where(['friends_followings.user_id' => $request->friend_id,'friends_followings.following_status' => 'approved'])
        ->whereNotIn('users.id',$blockedUserIDs)
        ->get();
        if($user->isNotEmpty()){
            $follow_status = null;
            foreach ($user as $users) {
                $friendFollowing = FriendsFollowing::join('users','users.id','friend_user_id')
                ->where(['friends_followings.user_id' => $request->user_id,'friends_followings.friend_user_id' => $users->id])->first();
                if($friendFollowing){
                    if($friendFollowing->following_status == 'pending'){$follow_status = 1;
                    }else{$follow_status = 2;}
                }else{$follow_status = 0;}
                $user_data[] = [
                    'id' => $users->id,
                    'first_name' => $users->first_name,
                    'last_name' => $users->last_name,
                    'profile_image' => $users->profile_image,
                    'follow_status' => $follow_status
                ];
            }
            return response()->json(['status' => 200,'data' => ['users' => $user_data],'message' => 'Data Fetched Successfully']);
        }else{
            return response()->json(['status' => 200,'data' => [],'message' => 'No Data Found.']);
        }
    }

    public function user_friends_following_search(Request $request){
        $request->validate(['friend_id'=>'required','keyword'=>'required']);
        $keyword = $request->keyword;
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $user = FriendsFollowing::join('users','users.id','friend_user_id')
        ->select('users.*','friends_followings.user_id','friends_followings.following_status')
        ->where(['friends_followings.user_id' => $request->friend_id,'friends_followings.following_status' => 'approved'])
        ->where(function($query) use ($keyword) {$query->where('users.first_name', 'LIKE', '%' . $keyword . '%')->orWhere('users.last_name', 'LIKE', '%' . $keyword . '%');})
        ->whereNotIn('users.id',$blockedUserIDs)
        ->where('users.id','!=',$request->user_id)->get();
        if($user->isNotEmpty()){
            $follow_status = null;
            foreach ($user as $users) {
                $friendFollowing = FriendsFollowing::join('users','users.id','friend_user_id')
                ->where(['friends_followings.user_id' => $request->user_id,'friends_followings.friend_user_id' => $users->id])->first();
                if($friendFollowing){
                    if($friendFollowing->following_status == 'pending'){$follow_status = 1;
                    }else{$follow_status = 2;}
                }else{$follow_status = 0;}
                $user_data[] = [
                    'id' => $users->id,
                    'first_name' => $users->first_name,
                    'last_name' => $users->last_name,
                    'profile_image' => $users->profile_image,
                    'follow_status' => $follow_status
                ];
            }
            return response()->json(['status' => 200,'data' => ['users' => $user_data],'message' => 'Data Fetched Successfully']);
        }else{
            return response()->json(['status' => 200,'data' => [],'message' => 'No Data Found.']);
        }
    }

    public function user_friends_followers(Request $request){
        $request->validate(['friend_id'=>'required']);
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $user = FriendsFollowers::join('users','users.id','friend_user_id')
        ->select('users.*','friends_followers.user_id','friends_followers.followers_status')
        ->where(['friends_followers.user_id' => $request->friend_id,'friends_followers.followers_status' => 'approved'])
        ->whereNotIn('users.id',$blockedUserIDs)
        ->get();
        if($user->isNotEmpty()){
            $follow_status = null;
            foreach ($user as $users) {
                $friendFollowers = FriendsFollowers::join('users','users.id','friend_user_id')
                ->where(['friends_followers.user_id' => $users->id,'friends_followers.friend_user_id' => $request->user_id])->first();
                if($friendFollowers){
                    if($friendFollowers->followers_status == 'pending'){$follow_status = 1;
                    }else{$follow_status = 2;}
                }else{$follow_status = 0;}
                $user_data[] = [
                    'id' => $users->id,
                    'first_name' => $users->first_name,
                    'last_name' => $users->last_name,
                    'profile_image' => $users->profile_image,
                    'follow_status' => $follow_status
                ];
            }
            return response()->json(['status' => 200,'data' => ['users' => $user_data],'message' => 'Data Fetched Successfully']);
        }else{
            return response()->json(['status' => 200,'data' => [],'message' => 'No Data Found.']);
        }
    }

    public function user_friends_followers_search(Request $request){
        $request->validate(['friend_id'=>'required','keyword'=>'required']);
        $keyword = $request->keyword;
      	$blockedUserIDs = UserBlockOrUnblock::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->pluck('friend_user_id')->toArray();
        $user = FriendsFollowers::join('users','users.id','friend_user_id')
        ->select('users.*','friends_followers.user_id','friends_followers.followers_status')
        ->where(['friends_followers.user_id' => $request->friend_id,'friends_followers.followers_status' => 'approved'])
        ->where(function($query) use ($keyword) {$query->where('users.first_name', 'LIKE', '%' . $keyword . '%')->orWhere('users.last_name', 'LIKE', '%' . $keyword . '%');})
        ->whereNotIn('users.id',$blockedUserIDs)
        ->where('users.id','!=',$request->user_id)->get();
        if($user->isNotEmpty()){
            $follow_status = null;
            foreach ($user as $users) {
                $friendFollowers = FriendsFollowers::join('users','users.id','friend_user_id')
                ->where(['friends_followers.user_id' => $users->id,'friends_followers.friend_user_id' => $request->user_id])->first();
                if($friendFollowers){
                    if($friendFollowers->followers_status == 'pending'){$follow_status = 1;
                    }else{$follow_status = 2;}
                }else{$follow_status = 0;}
                $user_data[] = [
                    'id' => $users->id,
                    'first_name' => $users->first_name,
                    'last_name' => $users->last_name,
                    'profile_image' => $users->profile_image,
                    'follow_status' => $follow_status
                ];
            }
            return response()->json(['status' => 200,'data' => ['users' => $user_data],'message' => 'Data Fetched Successfully']);
        }else{
            return response()->json(['status' => 200,'data' => [],'message' => 'No Data Found.']);
        }
    }
}
