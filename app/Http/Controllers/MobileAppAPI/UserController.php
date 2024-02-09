<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Models\Admin\TaskSubCategory;
use App\Models\Chat;
use App\Models\MobileApp\AddMembers;
use App\Models\MobileApp\AppFeedback;
use App\Models\MobileApp\AppShare;
use App\Models\MobileApp\AssignTasksToMembers;
use App\Models\MobileApp\FriendsFollowers;
use App\Models\MobileApp\FriendsFollowing;
use App\Models\MobileApp\Journal;
use App\Models\MobileApp\LookingFor;
use App\Models\MobileApp\Notification;
use App\Models\MobileApp\Tasks;
use App\Models\MobileApp\UserBlockOrUnblock;
use App\Models\MobileApp\UserEveningCheckIN;
use App\Models\MobileApp\UserMorningCheckIN;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  	public function app_feedback(Request $request){
        $request->validate(['feedback'=>'required']);
        AppFeedback::create($request->all());
        return response()->json(['status' => 200,'data' => [],'message' => 'Feedback Sent Successfully']);
    }
  
    public function app_share(Request $request){
        $request->validate(['shared_email'=>'required']);
        $user = User::where('email', $request->shared_email)->first();
        if($user){
            return response()->json(['status' => 200,'data' => [],'message' => 'Email-ID Already Exists']);
        }else{
            $user_check = AppShare::where('shared_email',$request->shared_email)->first();
            if($user_check){
                return response()->json(['status' => 200,'data' => [],'message' => 'Email-ID Already Exists']);
            }else{
                AppShare::create($request->all());
                return response()->json(['status' => 200,'data' => [],'message' => 'App Link Shared Successfully']);
            }
        }
    }
  
  	public function chat_file_upload(Request $request){
        if($request->hasFile('file_attachment')){
            $file = $request->file('file_attachment');
            $fileName = 'Attachment_'.rand(111111111111111,999999999999999).".".$file->getClientOriginalExtension();
            Chat::create(['file_attachment'=>$fileName]);
            $file->move('assets/chat_files',$fileName);
            return response()->json(['status' => 200,'data' => ['file_url' => url('assets/chat_files/')."/".$fileName],'message' => 'File Uploaded Successfully']);
        }
        return response()->json(['status' => 200,'data' => [],'message' => 'File Uploaded Successfully']);
    }
  
  	public function user_info_for_chat(Request $request){
      	$user = User::find($request->user_id);
        if($user){
            return response()->json([
              'status' => 200,
              'data' => [
                  'user_id' => $user->id,
                  'profile_image' => url("assets/images/profile/".$user->profile_image)
              ],
              'message' => 'Data Fetched Successfully'
          ]);
        }else{
            return response()->json(['status' => 200,'data' => null,'message' => 'User Not Found']);
        }
    }
  
  	public function user_info_as_per_media_id(Request $request){
        $user = User::where('email',$request->email)->first();
        if($user){
            $login_token = $user->createToken($request->email)->accessToken;
            $user->update($request->all());
            return response()->json(['status' => 200,'data' => ['user'=>$user,'token'=>$login_token],'message' => 'User Logged In Successfully']);
        }else{
            return response()->json(['status' => 200,'data' => null,'message' => 'User Data Not Found']);
        }
    }
  
  	public function user_block(Request $request){
        $request->validate(['friend_user_id' => 'required']);
        UserBlockOrUnblock::create($request->all());
        return response()->json(['status' => 200,'data' => [],'message' => 'User Block Successfully']);
    }

    public function user_unblock(Request $request){
        $request->validate(['friend_user_id' => 'required']);
        UserBlockOrUnblock::where([
            'user_id'=>$request->user_id,
            'friend_user_id'=>$request->friend_user_id
        ])->delete();
        return response()->json(['status' => 200,'data' => [],'message' => 'User Unblock Successfully']);
    }

    public function blocked_user_list(Request $request){
        $users = UserBlockOrUnblock::join('users','users.id','user_block_or_unblocks.friend_user_id')
        ->select('users.id as user_id','users.first_name','users.last_name','users.email','users.profile_image',)
        ->where('user_block_or_unblocks.user_id',$request->user_id)
        ->get();
        return response()->json(['status' => 200,'data' => $users,'message' => 'Data Fetched Successfully']);
    }

    public function user_delete(Request $request){
        User::find($request->user_id)->delete();
        AddMembers::where('user_id', $request->user_id)->delete();
        AppFeedback::where('user_id', $request->user_id)->delete();
        AppShare::where('user_id', $request->user_id)->delete();
        AssignTasksToMembers::where('user_id', $request->user_id)->delete();
        AssignTasksToMembers::where('user_id', $request->user_id)->delete();
        FriendsFollowing::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->delete();
        FriendsFollowers::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->delete();
        Journal::where('user_id', $request->user_id)->delete();
        LookingFor::where('user_id', $request->user_id)->delete();
        Notification::where('user_id', $request->user_id)->delete();
        Tasks::where('user_id', $request->user_id)->delete();
        TaskSubCategory::where('user_id', $request->user_id)->delete();
        UserBlockOrUnblock::where('user_id', $request->user_id)->orWhere('friend_user_id', $request->user_id)->delete();
        UserEveningCheckIN::where('user_id', $request->user_id)->delete();
        UserMorningCheckIN::where('user_id', $request->user_id)->delete();

        return response()->json([
            'status' => 200,
            'data' => [],
            'message' => 'User Permanently Deleted Successfully'
        ]);
    }
}
