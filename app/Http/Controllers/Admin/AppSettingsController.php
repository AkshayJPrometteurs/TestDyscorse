<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AppSettings;
use Illuminate\Http\Request;

class AppSettingsController extends Controller
{
    public function app_settings(){
        $data = AppSettings::first();
        return view('Admin.app-settings',compact('data'));
    }

    public function app_settings_save(Request $request){
        $request->validate(['terms_and_conditions' => 'required','privacy_and_policy' => 'required']);
        $data = AppSettings::first();
        if($data){
            AppSettings::find($data->id)->update($request->all());
            flash()->addSuccess('Data Updated Successfully');
        }else{
            AppSettings::create($request->all());
            flash()->addSuccess('Data Added Successfully');
        }
        return redirect()->back();
    }

    public function terms_and_condition_view(){
        $data = AppSettings::first();
        return view('terms-and-conditions',compact('data'));
    }

    public function privacy_and_policy_view(){
        $data = AppSettings::first();
        return view('privacy-and-policy',compact('data'));
    }

    public function help_view(){
        $data = AppSettings::first();
        return view('help',compact('data'));
    }
}
