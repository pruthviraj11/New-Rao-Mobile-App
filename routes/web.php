<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;


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

});
/* Route Apps */
