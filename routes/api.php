<?php

use App\Http\Controllers\Admin\UserAvatarController;
use App\Http\Controllers\MobileAppAPI\AuthController;
use App\Http\Controllers\MobileAppAPI\ChallengesController;
use App\Http\Controllers\MobileAppAPI\JournalController;
use App\Http\Controllers\MobileAppAPI\NotificationController;
use App\Http\Controllers\MobileAppAPI\ProfileController;
use App\Http\Controllers\MobileAppAPI\SplashScreenController;
use App\Http\Controllers\MobileAppAPI\TasksController;
use App\Http\Controllers\MobileAppAPI\UserController;
use App\Http\Controllers\MobileAppAPI\UserEveningCheckInController;
use App\Http\Controllers\MobileAppAPI\UserMorningCheckInController;
use App\Http\Controllers\MobileAppAPI\YourInsightsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// All Authentication Routes Provide Here.....
Route::controller(AuthController::class)->group(function(){
    Route::post('register','register');
    Route::post('login','login');
    Route::post('social_media_login','social_media_login');
    Route::post('forget_password','forget_password');
    Route::post('resend_otp','resend_otp');
    Route::post('otp_verification','otp_verification');
    Route::post('reset_password','reset_password');
});

Route::get('/splash_screen_questions',[SplashScreenController::class,'splash_screen_questions']);
Route::controller(UserController::class)->group(function(){
    Route::get('user_info_as_per_media_id','user_info_as_per_media_id');
});

Route::middleware('auth:api')->group(function(){
    Route::get('/user', function(Request $request){
        return response()->json([
            'status' => 200,
            'data' => ['user' => $request->user()],
            'message' => 'Authorized User Data Fetch Successfully'
        ]);
    });
    Route::post('logout',[AuthController::class,'logout']);
    Route::controller(UserController::class)->group(function(){
        Route::post('app_feedback','app_feedback');
        Route::post('app_share','app_share');
        Route::post('chat_file_upload','chat_file_upload');
        Route::get('user_info_for_chat','user_info_for_chat');
    });
    Route::middleware('userCheck')->group(function(){
        Route::controller(TasksController::class)->group(function(){
            Route::get('custom_task_create_view','custom_task_create_view');
            Route::post('custom_task_create','custom_task_create');
            Route::post('reset_tasks','reset_tasks');
            Route::post('task_remove','task_remove');
            Route::get('task_activities','task_activities');
            Route::post('task_activities_time_update','task_activities_time_update');
            Route::get('tracking_task_weekly_list','tracking_task_weekly_list');
            Route::post('tracking_task_status_update','tracking_task_status_update');
            Route::post('custom_task_sub_category','custom_task_sub_category');
            Route::get('home_screen','home_screen');
            Route::get('choose_member_list','choose_member_list');
            Route::post('assign_tasks_to_members','assign_tasks_to_members');
            Route::post('assign_tasks_to_members_accept','assign_tasks_to_members_accept');
            Route::post('assign_tasks_to_members_reject','assign_tasks_to_members_reject');
            Route::post('add_calender_data_store','add_calender_data_store');
            Route::post('task_overlapping_cancel','task_overlapping_cancel');
            Route::get('custom_task_images_list','custom_task_images_list');
        });
        Route::controller(ProfileController::class)->group(function(){
            Route::post('profile/edit_profile','edit_profile');
            Route::post('profile/remove_profile_image','remove_profile_image');
            Route::post('profile/avatar_to_profile_image','avatar_to_profile_image');
            Route::post('profile/add_members','add_members');
            Route::post('profile/members_accept','members_accept');
            Route::post('profile/members_reject','members_reject');
            Route::get('profile/friend_suggestion','friend_suggestion');
            Route::post('profile/friend_suggestion_follow','friend_suggestion_follow');
            Route::post('profile/friend_unfollow','friend_unfollow');
            Route::get('profile/following_list','following_list');
            Route::get('profile/followers_list','followers_list');
            Route::get('profile/followers_pending_requests_list','followers_pending_requests_list');
            Route::post('profile/approve_friend_request','approve_friend_request');
            Route::post('profile/reject_friend_request','reject_friend_request');
            Route::get('profile/user_profile','user_profile');
            Route::get('profile/user_following_search','user_following_search');
            Route::get('profile/user_followers_search','user_followers_search');
            Route::get('profile/friend_profile','friend_profile');
            Route::post('profile/update_user_fcm_token','update_user_fcm_token');
            Route::get('profile/friend_suggestion_search','friend_suggestion_search');
            Route::get('profile/user_friends_following','user_friends_following');
            Route::get('profile/user_friends_following_search','user_friends_following_search');
            Route::get('profile/user_friends_followers','user_friends_followers');
            Route::get('profile/user_friends_followers_search','user_friends_followers_search');
        });
        Route::controller(NotificationController::class)->group(function(){
            Route::post('notification_store','notification_store');
            Route::get('notification_list','notification_list');
        });
        Route::controller(JournalController::class)->group(function(){
            Route::post('journal_store','journal_store');
            Route::get('journal_list','journal_list');
            Route::get('journal_search','journal_search');
        });
        Route::controller(YourInsightsController::class)->group(function(){
            Route::get('your_insights','your_insights');
            Route::get('your_insights_for_week','your_insights_for_week');
            Route::get('your_insights_for_month','your_insights_for_month');
            Route::get('your_insights_for_quarterly','your_insights_for_quarterly');
            Route::get('your_insights_for_yearly','your_insights_for_yearly');
        });
        Route::controller(UserAvatarController::class)->group(function(){
            Route::get('get_user_avatars','get_user_avatars');
            Route::post('add_user_avatar','add_user_avatar');
        });
        Route::controller(UserMorningCheckInController::class)->group(function(){
            Route::post('user_morning_check_in_store','user_morning_check_in_store');
        });
        Route::controller(UserEveningCheckInController::class)->group(function(){
            Route::post('user_evening_check_in_store','user_evening_check_in_store');
        });
        Route::controller(UserController::class)->group(function(){
            //user block
            Route::post('user_block','user_block');
            Route::post('user_unblock','user_unblock');
            Route::get('blocked_user_list','blocked_user_list');
            Route::post('user_delete','user_delete');
        });
        Route::controller(ChallengesController::class)->group(function(){
            Route::post('create_challenge','create_challenge');
            Route::get('challenges_list','challenges_list');
            Route::get('challenges_suggestion_list','challenges_suggestion_list');
            Route::post('user_join_challenge','user_join_challenge');
            Route::post('user_unjoin_challenge','user_unjoin_challenge');
            Route::get('challenges_screen','challenges_screen');
            Route::post('delete_challenge','delete_challenge');
            Route::post('create_challenge_post','create_challenge_post');
            Route::get('challenge_detail','challenge_detail');
            Route::post('challenge_post_like','challenge_post_like');
            Route::post('challenge_post_comment','challenge_post_comment');
            Route::get('challenge_post_details','challenge_post_details');
            Route::get('joined_members_list','joined_members_list');
            Route::post('delete_member','delete_member');
            Route::post('complete_challenge','complete_challenge');
            Route::post('reset_reminder_time','reset_reminder_time');
            Route::post('reminder_push_notification','reminder_push_notification');
        });
    });
    Route::get('task_categories',[TasksController::class,'task_categories']);
});
