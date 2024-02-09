<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileApp\FriendsFollowers;
use App\Models\MobileApp\FriendsFollowing;
use App\Models\User;
use Illuminate\Support\Facades\File;

class AdminUserController extends Controller
{
    public function user_list_view(){
        $users = User::all();
        return view('Admin.users.users',compact('users'));
    }

    public function view_user(Request $request){
        $user = User::find($request->id);
        return response()->json(['user' => $user]);
    }

    public function edit_user($id){
        $user = User::find($id);
        return view('Admin.users.edit-user',compact('user'));
    }

    public function update_user(Request $request, $id){
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,'.$id.'',
            'gender' => 'required',
            'age' => 'required|numeric|min:10|max:100',
        ],['age.min' => 'Age must be greater than 10.','age.max' => 'Age must be less than 100.']);
        $user = User::find($id);
        $profileName = $user->profile_image;
        if($request->hasFile('profile_image')){
            if(File::exists(public_path()."/assets/images/profile/".$user->profile_image)){
                File::delete(public_path()."/assets/images/profile/".$user->profile_image);
            }
            $profileName = 'Profile_'.rand(1111111111,99999999999).".".$request->file('profile_image')->getClientOriginalExtension();
            $request->file('profile_image')->move('assets/images/profile', $profileName);
        }
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'gender' => $request->gender,
            'age' => $request->age,
            'privacy' => $request->privacy,
            'profile_image' => $profileName,
        ]);
        flash()->addSuccess('User Data Updated Successfully');
        return redirect()->route('user_list_view');
    }

    public function delete_user(Request $request){
        $user = User::find($request->id);
        if(File::exists(public_path().'assets/images/profile/'.$user->profile_image)){
            File::delete(public_path().'assets/images/profile/'.$user->profile_image);
        }
        FriendsFollowers::where('user_id',$request->id)->orWhere('friend_user_id',$request->id)->delete();
        FriendsFollowing::where('user_id',$request->id)->orWhere('friend_user_id',$request->id)->delete();
        $user->delete();
        flash()->addSuccess('User Deleted Successfully');
        return redirect()->route('user_list_view');
    }
}
