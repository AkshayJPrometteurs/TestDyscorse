<?php

use App\Http\Controllers\Admin\AdminAppFeedbackController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSplashScreenQuestionsController;
use App\Http\Controllers\Admin\AdminTasksController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminUserMembersController;
use App\Http\Controllers\Admin\AppSettingsController;
use App\Http\Controllers\Admin\EmailSendsController;
use App\Http\Controllers\Admin\PointCalculationsController;
use App\Http\Controllers\Admin\SloganController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login',[AdminAuthController::class,'login_page'])->name('login_page');
Route::post('/forget-password',[AdminAuthController::class,'admin_forget_password'])->name('admin_forget_password');
Route::post('/authentication',[AdminAuthController::class,'login'])->name('authentication');
Route::controller(AppSettingsController::class)->group(function(){
    Route::get('/terms-and-conditions','terms_and_condition_view')->name('terms_and_condition_view');
    Route::get('/privacy-and-policy','privacy_and_policy_view')->name('privacy_and_policy_view');
    Route::get('/help','help_view')->name('help_view');
});
Route::middleware('AdminAuth')->group(function(){
    Route::get('/',[AdminDashboardController::class,'dashboard'])->name('dashboard');
    Route::post('/logout',[AdminAuthController::class,'logout'])->name('logout');
    Route::controller(AdminUserController::class)->group(function(){
        Route::prefix('user')->group(function(){
            Route::get('/lists','user_list_view')->name('user_list_view');
            Route::post('/delete-user','delete_user')->name('delete_user');
            Route::get('/view-user','view_user')->name('view_user');
            Route::get('/edit-user/{id}','edit_user')->name('edit_user');
            Route::post('/update-user/{id}','update_user')->name('update_user');
        });
    });
    Route::controller(AdminSplashScreenQuestionsController::class)->group(function(){
        Route::prefix('splash-screen-questions')->group(function(){
            Route::get('/lists','splash_screen_questions')->name('splash_screen_questions');
            Route::get('/add-question','add_question')->name('add_question');
            Route::post('/save-question','save_question')->name('save_question');
            Route::post('/delete-question','delete_question')->name('delete_question');
            Route::get('/edit-question/{slug}','edit_question')->name('edit_question');
            Route::post('/update-question/{id}','update_question')->name('update_question');
          	Route::get('/user-answers-list','ssq_user_answers_list')->name('ssq_user_answers_list');
            Route::get('/view-user-answers-list/{id}','ssq_view_user_answers_list')->name('ssq_view_user_answers_list');
        });
    });
    Route::controller(AdminUserMembersController::class)->group(function(){
        Route::prefix('user/members')->group(function(){
            Route::get('/lists','members_lists')->name('members_lists');
            Route::get('/edit-member/{id}','edit_member')->name('edit_member');
            Route::post('/update-member/{id}','update_member')->name('update_member');
            Route::post('/delete-member','delete_member')->name('delete_member');
        });
    });
    Route::controller(AdminTasksController::class)->group(function(){
        Route::prefix('tasks/category')->group(function(){
            Route::get('/lists','task_category_list')->name('task_category_list');
            Route::get('/add-category','add_task_category')->name('add_task_category');
            Route::post('/save-task-category','save_task_category')->name('save_task_category');
            Route::get('/edit-category/{slug}','edit_task_category')->name('edit_task_category');
            Route::post('/update-task-category/{id}','update_task_category')->name('update_task_category');
            Route::post('/delete-task-category','delete_task_category')->name('delete_task_category');
        });
      	Route::get('/tasks-list','main_tasks_list')->name('main_tasks_list');
        Route::prefix('tasks/sub-category')->group(function(){
            Route::get('/lists','task_subcategory_list')->name('task_subcategory_list');
            Route::get('/add-subcategory','add_task_subcategory')->name('add_task_subcategory');
            Route::post('/save-task-subcategory','save_task_subcategory')->name('save_task_subcategory');
            Route::get('/edit-subcategory/{slug}','edit_task_subcategory')->name('edit_task_subcategory');
            Route::post('/update-task-subcategory/{id}','update_task_subcategory')->name('update_task_subcategory');
            Route::post('/delete-task-subcategory','delete_task_subcategory')->name('delete_task_subcategory');
        });
    });
    Route::controller(AdminController::class)->group(function(){
        Route::prefix('users')->group(function(){
            Route::get('/lists','user_list_for_ff')->name('user_list_for_ff');
            Route::get('/following-lists/{id}','user_following_list')->name('user_following_list');
            Route::get('/followers-lists/{id}','user_followers_list')->name('user_followers_list');
            Route::get('/request-pending-lists/{id}','user_request_pending_list')->name('user_request_pending_list');
        });
    });
  	Route::controller(EmailSendsController::class)->group(function(){
        Route::get('emails-sends/user_list','user_list_for_emails')->name('user_list_for_emails');
        Route::get('sent_now_emails','sent_now_emails')->name('sent_now_emails');
        Route::post('send_email_with_data','send_email_with_data')->name('send_email_with_data');
    });
  	Route::controller(PointCalculationsController::class)->group(function(){
        Route::get('point_calculations','point_calculation_view')->name('point_calculation_view');
        Route::post('point_calculation_store','point_calculation_store')->name('point_calculation_store');
        Route::post('delete_milestone','delete_milestone')->name('delete_milestone');
    });
  	Route::controller(AdminAppFeedbackController::class)->group(function(){
        Route::get('/feedback-list','feedback_list')->name('feedback_list');
        Route::get('/edit-feedback/{id}','edit_feedback')->name('edit_feedback');
        Route::post('/update-feedback/{id}','update_feedback')->name('update_feedback');
        Route::post('/delete-feedback','delete_feedback')->name('delete_feedback');
    });
  	Route::controller(AppSettingsController::class)->group(function(){
        Route::get('app-settings','app_settings')->name('app_settings');
        Route::post('app-settings-save','app_settings_save')->name('app_settings_save');
    });
  	Route::controller(SloganController::class)->group(function(){
        Route::get('slogans/list','slogan_list')->name('slogan_list');
        Route::get('slogans/add','add_slogan')->name('add_slogan');
        Route::post('slogans/save','save_slogan')->name('save_slogan');
        Route::post('slogans/delete','delete_slogan')->name('delete_slogan');
    });
});

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return 'Application cache has been cleared';
});

Route::get('/config-cache', function() {
    Artisan::call('config:cache');
    return 'Config cache has been cleared';
});
