<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\FriendsFollowers;
use App\Models\MobileApp\FriendsFollowing;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function user_list_for_ff(){
        $users = User::all();
        return view('Admin.users.users-list-for-ff',compact('users'));
    }

    public function user_following_list(Request $request, $id){
        $following_list = FriendsFollowing::join('users','users.id','friend_user_id')
        ->select('users.*')
        ->where('friends_followings.user_id', $id)
        ->where('friends_followings.following_status','approved')
        ->get();
        return view('Admin.users.user-following-list',compact('following_list'));
    }

    public function user_followers_list(Request $request, $id){
        $followers_list = FriendsFollowers::join('users','users.id','friend_user_id')
        ->select('users.*')
        ->where('friends_followers.user_id', $id)
        ->where('friends_followers.followers_status', 'approved')
        ->get();
        return view('Admin.users.user-followers-list',compact('followers_list'));
    }

    public function user_request_pending_list(Request $request, $id){
        $request_pending_list = FriendsFollowers::join('users','users.id','friend_user_id')
        ->select('users.*','friends_followers.created_at')
        ->where('friends_followers.user_id', $id)
        ->where('friends_followers.followers_status', 'pending')
        ->get();
        return view('Admin.users.user-followers-pending-requests',compact('request_pending_list'));
    }
}
