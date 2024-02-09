<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\UserAvatar;
use Illuminate\Http\Request;

class UserAvatarController extends Controller
{
    public function get_user_avatars(){
        $avatar = UserAvatar::all();
        foreach($avatar as $uavatar){
            $data[] = ['id' => $uavatar->id,'avatar_name'=>$uavatar->avatar_name,'avatar_image' => url('/assets/images/profile')."/".$uavatar->avatar_name];
        }
        return response()->json(['status'=>200,'data'=>['avatars'=>$data],'message'=>'Data Fetched Successfully']);
    }
    public function add_user_avatar(Request $request){
        $request->validate(['avatar'=>'required']);
        $avatar_last_data = UserAvatar::orderBy('id','desc')->first();
        $avatar_no = null;
        $avatar_last_data ? $avatar_no = $avatar_last_data->id : $avatar_no = 0;
        if($request->hasFile('avatar')){
            $avatarName = 'Avatar_'.($avatar_no+1).".".$request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move('assets/images/profile', $avatarName);
        }
        $avatar = UserAvatar::create(['avatar_name'=>$avatarName]);
        return response()->json(['status'=>200,'data'=>$avatar]);
    }
}
