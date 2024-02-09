<?php
namespace App\Http\Controllers\MobileAppAPI;
use App\Http\Controllers\Controller;
use App\Http\Requests\MobileApp\LoginRequest;
use App\Http\Requests\MobileApp\RegisterRequest;
use App\Mail\MobileApp\ResetPasswordOTP;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{  
  	public function register(RegisterRequest $request){
        $validated = $request->validated();
        $validated['privacy'] = 'Public';
        $validated['max_streak'] = 0;
        $user = User::create($validated);
        $token = $user->createToken(strtolower($request->first_name.'-'.$request->last_name))->accessToken;
        return response()->json(['status' => 200,
            'data' => ['user' => $user,'token' => $token],
            'message' => 'Your Account Created Successfully'
        ]);
    }

    public function login(LoginRequest $request){
        $request->validated();
        $data = [];
        if($request->auth_type == 'google'){
            $data = ['google_id'=>221312321];
            $jsonData = ['status' => 200,'data' => $data,'message' => 'Login Successfully'];
        }else if($request->auth_type == 'facebook'){
            $data = ['facebook_id'=>8468465454];
            $jsonData = ['status' => 200,'data' => $data,'message' => 'Login Successfully'];
        }else{
            $user = User::where('email', $request->email)->first();
            if($user && Hash::check($request->password, $user->password)){
                $token = $user->createToken($request->email)->accessToken;
                $data = ['user'=>$user,'token'=>$token];
                $jsonData = ['status' => 200,'data' => $data,'message' => 'Login Successfully'];
            }else{$jsonData = ['status' => 401,'data' => [],'message' => 'Invalid Credentials'];}
        }
        return response()->json($jsonData);
    }
  
  
  	public function social_media_login(Request $request){
        $email_verify = User::where('email', $request->email)->first();
        if($email_verify){
            $login_token = $email_verify->createToken($request->email)->accessToken;
            $update_data = $request->except('media_id');
            $email_verify->update($update_data);
            return response()->json(['status' => 200,'data' => ['user'=>$email_verify,'token'=>$login_token],'message' => 'User Logged In Successfully']);
        }else{
          	if(isset($request->media_id)){
              	$media_id_verify = User::where('media_id', $request->media_id)->first();
              	if($media_id_verify){
                  	$login_token = $media_id_verify->createToken($request->email)->accessToken;
                    $update_data = $request->except('email');
                    $media_id_verify->update($update_data);
                    return response()->json(['status' => 200,'data' => ['user'=>$media_id_verify,'token'=>$login_token],'message' => 'User Logged In Successfully']);
                }else{
                  	if(isset($request->email)){
                      	$request->merge(['privacy' => 'Public']);
                        $user_new = User::create($request->all());
                        $view_user = User::where('email', $request->email)->first();
                        $register_token = $user_new->createToken(strtolower($request->first_name.'-'.$request->last_name))->accessToken;
                        return response()->json(['status' => 200,'data' => ['user' => $view_user,'token' => $register_token],'message' => 'User Registered Successfully']);
                    }else{
                      	return response()->json(['status' => 500,'data' => [],'message' => 'Email-ID can not be empty']);
                    }
                }
            }else{
                $request->merge(['privacy' => 'Public']);
                $user_new = User::create($request->all());
                $view_user = User::where('email', $request->email)->first();
                $register_token = $user_new->createToken(strtolower($request->first_name.'-'.$request->last_name))->accessToken;
                return response()->json(['status' => 200,'data' => ['user' => $view_user,'token' => $register_token],'message' => 'User Registered Successfully']);
            }
        }
    }

    public function forget_password(Request $request){
        $request->validate(['email'=>'required']);
        $user = User::where('email', $request->email)->first();
        if($user){
            try{
                $otp = rand('1111','9999');
                $user->update(['verify_otp' => $otp]);
                $data = ['name'=> $user->first_name." ".$user->last_name,'otp' => $otp];
                Mail::to($request->email)->send(new ResetPasswordOTP($data));
                $jsonData = ['status' => 200,'data' => [],'message' => 'OTP Sends On Your Email-ID'];
            }
            catch(Exception $e){$jsonData = ['status' => 500,'data' => [],'error' => $e->getMessage()];}
        }else{$jsonData = ['status' => 404,'data' => [],'message' => 'User Account Not Found'];}
        return response()->json($jsonData);
    }

    public function resend_otp(Request $request){
        $request->validate(['email'=>'required']);
        $user = User::where('email', $request->email)->first();
        if($user){
            try{
                $otp = rand('1111','9999');
                $user->update(['verify_otp' => $otp]);
                $data = ['name'=> $user->first_name." ".$user->last_name,'otp' => $otp];
                Mail::to($request->email)->send(new ResetPasswordOTP($data));
                $jsonData = ['status' => 200,'data' => [],'message' => 'OTP Resend On Your Email-ID'];
            }
            catch(Exception $e){$jsonData = ['status' => 500,'data' => [],'error' => $e->getMessage()];}
        }else{$jsonData = ['status' => 500,'data' => [],'message' => 'User Account Not Found'];}
        return response()->json($jsonData);
    }

    public function otp_verification(Request $request){
        $request->validate(['email'=>'required','otp'=>'required']);
        $user = User::where('email', $request->email)->first();
        if($user){
            if($request->otp == $user->verify_otp){
                $user->update(['verify_otp'=>null]);
                $jsonData = ['status' => 200,'data' => [],'message' => 'OTP Verified Successfully'];
            }else{$jsonData = ['status' => 500,'data' => [],'message' => 'Invalid OTP'];}
        }else{$jsonData = ['status' => 500,'data' => [],'message' => 'User Account Not Found'];}
        return response()->json($jsonData);
    }

    public function reset_password(Request $request){
        $request->validate(['email'=>'required','password'=>'required']);
        $user = User::where('email', $request->email)->first();
        if($user){
            if(Hash::check($request->password, $user->password)){
                $jsonData = ['status' => 400,'data' => [],'message' => 'You are already use this password.'];
            }else{
                $user->update(['password'=>Hash::make($request->password)]);
                $jsonData = ['status' => 200,'data' => [],'message' => 'Your Password Has Been Reset Successfully'];
            }
        }else{$jsonData = ['status' => 500,'data' => [],'message' => 'User Account Not Found'];}
        return response()->json($jsonData);
    }

    public function logout(Request $request){
      	$user = User::find($request->user()->id);
      	$user->update(['fcm_token' => null]);
        $request->user()->tokens->each(function ($token, $key) {$token->delete();});
        return response()->json(['status' => 200,'data' => [],'message' => 'Logout Successfully']);
    }
}
