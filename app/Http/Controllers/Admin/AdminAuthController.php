<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Mail\Admin\ForgetPassword;
use App\Models\Admin\AdminLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends Controller
{
    public function dashboard(){
        return view('Admin.dashboard');
    }

    public function login_page(){
        if(Auth::guard('webadmin')->check()){
            return redirect('/');
        }
        return view('Admin.auth.login');
    }

    public function login(LoginRequest $request){
        $request->validated();
        $credentials = ['email' => $request->email,'password' => $request->password];
        if(Auth::guard('webadmin')->attempt($credentials)){
            if($request->remember_me == 1){
                Cookie::queue(Cookie::make('adymsicnorse_edmymsaciolrse',$request->email));
                Cookie::queue(Cookie::make('adymsicnorse_pdayssscwoorrsde',$request->password));
            }else{
                Cookie::queue(Cookie::forget('adymsicnorse_edmymsaciolrse'));
                Cookie::queue(Cookie::forget('adymsicnorse_pdayssscwoorrsde'));
            }
            flash()->addSuccess('Admin Login Successfully');
            return redirect()->intended('/');
        }
        flash()->addError('Invalid credentials');
        return redirect("login");
    }

    public function logout(){
        Session::flush();
        Auth::guard('webadmin')->logout();
        flash()->addSuccess('Admin Logout Successfully');
        return redirect('/login');
    }
  
  	public function admin_forget_password(Request $request){
        $admin = AdminLogin::where('email',$request->user_email)->first();
        if($admin){
            $data = [
                'email' => $admin->email,
                'password' => $admin->original_password,
                'url' => route('login_page')
            ];
            Mail::to($request->user_email)->send(new ForgetPassword($data));
            flash()->addSuccess('Credentials sent on your email-id.');
        }else{
            flash()->addError('Email-ID not registered');
        }
        return redirect('/login');
    }
}
