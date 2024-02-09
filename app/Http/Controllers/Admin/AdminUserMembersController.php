<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\AddMembers;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserMembersController extends Controller
{
    public function members_lists(){
        $members = AddMembers::join('users','users.id','user_id')
        ->select('add_members.*','users.first_name','users.last_name')
        ->get();
        return view('Admin.users.members.members',compact('members'));
    }

    public function edit_member($id){
        $member = AddMembers::find($id);
        return view('Admin.users.members.edit-member',compact('member'));
    }

    public function update_member(Request $request, $id){
        $request->validate([
            'member_name' => 'required',
            'member_email' => 'required|unique:add_members,member_email,'.$id.'',
            'member_mobile' => 'required|unique:add_members,member_mobile,'.$id.'',
        ]);
        $member = AddMembers::find($id);
        $member->update($request->all());
        flash()->addSuccess('Member Updated Successfully');
        return redirect()->route('members_lists');
    }

    public function delete_member(Request $request){
        AddMembers::find($request->id)->delete();
        flash()->addSuccess('Member Deleted Successfully');
        return redirect()->route('members_lists');
    }
}
