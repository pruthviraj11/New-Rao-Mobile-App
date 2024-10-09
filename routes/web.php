<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuccessStoriesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;

/*
|--------------------------------------------------------------------------
| Web Routesf
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Main Page Route
Route::get('/', function () {
    return redirect('/login');
});

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::any('/logout', [LoginController::class, 'logout'])->name('logout');

// Forgot Password routes
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Reset Password routes
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');



Route::group(['prefix' => 'auth'], function () {
    Route::get('login-basic', [AuthenticationController::class, 'login_basic'])->name('auth-login-basic');
    Route::get('login-cover', [AuthenticationController::class, 'login_cover'])->name('auth-login-cover');
    Route::get('register-basic', [AuthenticationController::class, 'register_basic'])->name('auth-register-basic');
    Route::get('register-cover', [AuthenticationController::class, 'register_cover'])->name('auth-register-cover');
    Route::get('forgot-password-basic', [AuthenticationController::class, 'forgot_password_basic'])->name('auth-forgot-password-basic');
    Route::get('forgot-password-cover', [AuthenticationController::class, 'forgot_password_cover'])->name('auth-forgot-password-cover');
    Route::get('reset-password-basic', [AuthenticationController::class, 'reset_password_basic'])->name('auth-reset-password-basic');
    Route::get('reset-password-cover', [AuthenticationController::class, 'reset_password_cover'])->name('auth-reset-password-cover');
    Route::get('verify-email-basic', [AuthenticationController::class, 'verify_email_basic'])->name('auth-verify-email-basic');
    Route::get('verify-email-cover', [AuthenticationController::class, 'verify_email_cover'])->name('auth-verify-email-cover');
    Route::get('two-steps-basic', [AuthenticationController::class, 'two_steps_basic'])->name('auth-two-steps-basic');
    Route::get('two-steps-cover', [AuthenticationController::class, 'two_steps_cover'])->name('auth-two-steps-cover');
    Route::get('register-multisteps', [AuthenticationController::class, 'register_multi_steps'])->name('auth-register-multisteps');
    Route::get('lock-screen', [AuthenticationController::class, 'lock_screen'])->name('auth-lock_screen');
});




Route::group(['prefix' => 'app', 'middleware' => 'auth'], function () {
    Route::get('permissions', [RoleController::class, 'permissions_list'])->name('app-permissions-list');
    Route::get('roles/list', [RoleController::class, 'index'])->name('app-roles-list');
    Route::get('send/mail', [MailController::class, 'sendMail'])->name('send-mail');


    Route::get('/profile/{encrypted_id}', [UsersController::class, 'profile'])->name('profile.show');
    Route::post('/profile/update/{encrypted_id}', [UsersController::class, 'updateProfile'])->name('profile-update');

    // =============================================================================================================================

    //   ROLE AND USER CONTROLLER

    // =============================================================================================================================

    // Roles Start
    Route::get('roles/list', [RoleController::class, 'index'])->name('app-roles-list');
    Route::get('roles/getAll', [RoleController::class, 'getAll'])->name('app-roles-get-all');
    Route::post('roles/store', [RoleController::class, 'store'])->name('app-roles-store');
    Route::get('roles/add', [RoleController::class, 'create'])->name('app-roles-add');
    Route::get('roles/edit/{encrypted_id}', [RoleController::class, 'edit'])->name('app-roles-edit');
    Route::put('roles/update/{encrypted_id}', [RoleController::class, 'update'])->name('app-roles-update');
    Route::get('roles/destroy/{encrypted_id}', [RoleController::class, 'destroy'])->name('app-roles-delete');
    /* Roles Routes End */

    //User start
    Route::get('users/list', [UsersController::class, 'index'])->name('app-users-list');
    Route::get('users/add', [UsersController::class, 'create'])->name('app-users-add');
    Route::post('users/store', [UsersController::class, 'store'])->name('app-users-store');
    Route::get('users/edit/{encrypted_id}', [UsersController::class, 'edit'])->name('app-users-edit');
    Route::put('users/update/{encrypted_id}', [UsersController::class, 'update'])->name('app-users-update');
    Route::get('users/destroy/{encrypted_id}', [UsersController::class, 'destroy'])->name('app-users-destroy');
    Route::get('users/getAll', [UsersController::class, 'getAll'])->name('app-users-get-all');
    //User End


    //Sliders Start
    Route::get('sliders/list', [SlideController::class, 'index'])->name('app-sliders-list');
    Route::get('sliders/getAll', [SlideController::class, 'getAll'])->name('app-sliders-get-all');
    Route::post('sliders/store', [SlideController::class, 'store'])->name('app-sliders-store');
    Route::get('sliders/add', [SlideController::class, 'create'])->name('app-sliders-add');
    Route::get('sliders/edit/{encrypted_id}', [SlideController::class, 'edit'])->name('app-sliders-edit');
    Route::put('sliders/update/{encrypted_id}', [SlideController::class, 'update'])->name('app-sliders-update');
    Route::get('sliders/destroy/{encrypted_id}', [SlideController::class, 'destroy'])->name('app-sliders-delete');
    Route::get('sliders/{id}', [SlideController::class, 'destroyimage'])->name('sliders.destroyimage');
    //Slider End


    //Client Type Start
    Route::get('client-types/list', [ClientTypeController::class, 'index'])->name('app-client-types-list');
    Route::get('client-types/getAll', [ClientTypeController::class, 'getAll'])->name('app-client-types-get-all');
    Route::post('client-types/store', [ClientTypeController::class, 'store'])->name('app-client-types-store');
    Route::get('client-types/add', [ClientTypeController::class, 'create'])->name('app-client-types-add');
    Route::get('client-types/edit/{encrypted_id}', [ClientTypeController::class, 'edit'])->name('app-client-types-edit');
    Route::put('client-types/update/{encrypted_id}', [ClientTypeController::class, 'update'])->name('app-client-types-update');
    Route::get('client-types/destroy/{encrypted_id}', [ClientTypeController::class, 'destroy'])->name('app-client-types-delete');
    //Client Type End

    //news-categories Type End
    Route::get('news-categories/list', [NewsCategoryController::class, 'index'])->name('app-news-categories-list');
    Route::get('news-categories/getAll', [NewsCategoryController::class, 'getAll'])->name('app-news-categories-get-all');
    Route::post('news-categories/store', [NewsCategoryController::class, 'store'])->name('app-news-categories-store');
    Route::get('news-categories/add', [NewsCategoryController::class, 'create'])->name('app-news-categories-add');
    Route::get('news-categories/edit/{id}', [NewsCategoryController::class, 'edit'])->name('app-news-categories-edit');
    Route::put('news-categories/update/{id}', [NewsCategoryController::class, 'update'])->name('app-news-categories-update');
    Route::get('news-categories/destroy/{id}', [NewsCategoryController::class, 'destroy'])->name('app-news-categories-delete');
    //news-categories Type End

    //success-stories Type End
    Route::get('success-stories/list', [SuccessStoriesController::class, 'index'])->name('app-success-stories-list');
    Route::get('success-stories/getAll', [SuccessStoriesController::class, 'getAll'])->name('app-success-stories-get-all');
    Route::post('success-stories/store', [SuccessStoriesController::class, 'store'])->name('app-success-stories-store');
    Route::get('success-stories/add', [SuccessStoriesController::class, 'create'])->name('app-success-stories-add');
    Route::get('success-stories/edit/{encrypted_id}', [SuccessStoriesController::class, 'edit'])->name('app-success-stories-edit');
    Route::put('success-stories/update/{encrypted_id}', [SuccessStoriesController::class, 'update'])->name('app-success-stories-update');
    Route::get('success-stories/destroy/{encrypted_id}', [SuccessStoriesController::class, 'destroy'])->name('app-success-stories-delete');
    //success-stories Type End

     //events Type End
     Route::get('events/list', [EventsController::class, 'index'])->name('app-events-list');
     Route::get('events/getAll', [EventsController::class, 'getAll'])->name('app-events-get-all');
     Route::post('events/store', [EventsController::class, 'store'])->name('app-events-store');
     Route::get('events/add', [EventsController::class, 'create'])->name('app-events-add');
     Route::get('events/edit/{encrypted_id}', [EventsController::class, 'edit'])->name('app-events-edit');
     Route::put('events/update/{encrypted_id}', [EventsController::class, 'update'])->name('app-events-update');
     Route::get('events/destroy/{encrypted_id}', [EventsController::class, 'destroy'])->name('app-events-delete');
     //events Type End

    //news Type End
    Route::get('news/list', [NewsController::class, 'index'])->name('app-news-list');
    Route::get('news/getAll', [NewsController::class, 'getAll'])->name('app-news-get-all');
    Route::post('news/store', [NewsController::class, 'store'])->name('app-news-store');
    Route::get('news/add', [NewsController::class, 'create'])->name('app-news-add');
    Route::get('news/edit/{encrypted_id}', [NewsController::class, 'edit'])->name('app-news-edit');
    Route::put('news/update/{encrypted_id}', [NewsController::class, 'update'])->name('app-news-update');
    Route::get('news/destroy/{encrypted_id}', [NewsController::class, 'destroy'])->name('app-news-delete');
    Route::get('news/{encrypted_id}', [NewsController::class, 'destroyimage'])->name('news.destroyimage');
    //news Type End

    //faq-category Type End
    Route::get('faq-categories/list', [FaqCategoryController::class, 'index'])->name('app-faq-categories-list');
    Route::get('faq-categories/getAll', [FaqCategoryController::class, 'getAll'])->name('app-faq-categories-get-all');
    Route::post('faq-categories/store', [FaqCategoryController::class, 'store'])->name('app-faq-categories-store');
    Route::get('faq-categories/add', [FaqCategoryController::class, 'create'])->name('app-faq-categories-add');
    Route::get('faq-categories/edit/{encrypted_id}', [FaqCategoryController::class, 'edit'])->name('app-faq-categories-edit');
    Route::put('faq-categories/update/{encrypted_id}', [FaqCategoryController::class, 'update'])->name('app-faq-categories-update');
    Route::get('faq-categories/destroy/{encrypted_id}', [FaqCategoryController::class, 'destroy'])->name('app-faq-categories-delete');
    Route::get('faq-categories/{encrypted_id}', [FaqCategoryController::class, 'destroyimage'])->name('faq-categories.destroyimage');
    //faq-category Type End


    //faq Type End
    Route::get('faq/list', [FaqController::class, 'index'])->name('app-faq-list');
    Route::get('faq/getAll', [FaqController::class, 'getAll'])->name('app-faq-get-all');
    Route::post('faq/store', [FaqController::class, 'store'])->name('app-faq-store');
    Route::get('faq/add', [FaqController::class, 'create'])->name('app-faq-add');
    Route::get('faq/edit/{encrypted_id}', [FaqController::class, 'edit'])->name('app-faq-edit');
    Route::put('faq/update/{encrypted_id}', [FaqController::class, 'update'])->name('app-faq-update');
    Route::get('faq/destroy/{encrypted_id}', [FaqController::class, 'destroy'])->name('app-faq-delete');
    Route::get('faq/{encrypted_id}', [FaqController::class, 'destroyimage'])->name('faq.destroyimage');
    //faq Type End

});
/* Route Apps */
