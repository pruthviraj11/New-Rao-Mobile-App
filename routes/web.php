<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdvisorController;
use App\Http\Controllers\ApplicationStatusesController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DefaultDocumentsController;
use App\Http\Controllers\DrawsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\FCMTokensController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\OtherStakeholdersController;
use App\Http\Controllers\OurServicesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuccessStoriesController;
use App\Http\Controllers\UploadedDocumentsController;
use App\Http\Controllers\UserDocumentsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\InternalProgramStatusController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeServiceController;

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
    Route::post('app-role-bulk-delete', [RoleController::class, 'bulkDelete'])->name('app-roles-destroy');

    Route::get('send/mail', [MailController::class, 'sendMail'])->name('send-mail');


    Route::get('/profile/{encrypted_id}', [UsersController::class, 'profile'])->name('profile.show');
    Route::get('users/application_journey/{encrypted_id}', [UsersController::class, 'applicationJourney'])->name('users.application_journey');
    Route::get('users/program_journey/{id}/{programId?}', [UsersController::class, 'programJourney'])->name('users.program_journey');
    Route::post('block/users', [UsersController::class, 'blockUser'])->name('block.users');
    Route::post('users/store_status', [UsersController::class, 'storeStatus'])->name('users.store_status');
    Route::get('restrict/users/screen/{id}', [UsersController::class, 'restrictScreens'])->name('users.restricted.screen');
    Route::post('restrict/users/screen/store', [UsersController::class, 'restrictScreensStore'])->name('users.restricted.screen.store');
    Route::post('clients-import-store', [UsersController::class, 'importClientStore'])->name('users.import.store');
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
    Route::post('app-users-bulk-delete', [UsersController::class, 'bulkDelete'])->name('app-users-bulk-destroy');
    //User End


    //Sliders Start
    Route::post('app-slide-bulk-delete', [SlideController::class, 'bulkDelete'])->name('app-sliders-bluk-destroy');
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
    Route::post('app-client-types-bulk-delete', [ClientTypeController::class, 'bulkDelete'])->name('app-client-types-destroy');

    //Client Type End

    //news-categories Type End
    Route::get('news-categories/list', [NewsCategoryController::class, 'index'])->name('app-news-categories-list');
    Route::get('news-categories/getAll', [NewsCategoryController::class, 'getAll'])->name('app-news-categories-get-all');
    Route::post('news-categories/store', [NewsCategoryController::class, 'store'])->name('app-news-categories-store');
    Route::get('news-categories/add', [NewsCategoryController::class, 'create'])->name('app-news-categories-add');
    Route::get('news-categories/edit/{id}', [NewsCategoryController::class, 'edit'])->name('app-news-categories-edit');
    Route::put('news-categories/update/{id}', [NewsCategoryController::class, 'update'])->name('app-news-categories-update');
    Route::get('news-categories/destroy/{id}', [NewsCategoryController::class, 'destroy'])->name('app-news-categories-delete');
    Route::post('app-news-categories-bulk-delete', [NewsCategoryController::class, 'bulkDelete'])->name('app-news-categories-bluk-destroy');
    //news-categories Type End

    //success-stories Type End
    Route::get('success-stories/list', [SuccessStoriesController::class, 'index'])->name('app-success-stories-list');
    Route::get('success-stories/getAll', [SuccessStoriesController::class, 'getAll'])->name('app-success-stories-get-all');
    Route::post('success-stories/store', [SuccessStoriesController::class, 'store'])->name('app-success-stories-store');
    Route::get('success-stories/add', [SuccessStoriesController::class, 'create'])->name('app-success-stories-add');
    Route::get('success-stories/edit/{encrypted_id}', [SuccessStoriesController::class, 'edit'])->name('app-success-stories-edit');
    Route::put('success-stories/update/{encrypted_id}', [SuccessStoriesController::class, 'update'])->name('app-success-stories-update');
    Route::get('success-stories/destroy/{encrypted_id}', [SuccessStoriesController::class, 'destroy'])->name('app-success-stories-delete');
    Route::post('app-success-stories-bulk-delete', [SuccessStoriesController::class, 'bulkDelete'])->name('app-success-stories-bluk-destroy');

    //success-stories Type End

    //events Type End
    Route::get('events/list', [EventsController::class, 'index'])->name('app-events-list');
    Route::get('events/getAll', [EventsController::class, 'getAll'])->name('app-events-get-all');
    Route::post('events/store', [EventsController::class, 'store'])->name('app-events-store');
    Route::get('events/add', [EventsController::class, 'create'])->name('app-events-add');
    Route::get('events/edit/{encrypted_id}', [EventsController::class, 'edit'])->name('app-events-edit');
    Route::put('events/update/{encrypted_id}', [EventsController::class, 'update'])->name('app-events-update');
    Route::get('events/destroy/{encrypted_id}', [EventsController::class, 'destroy'])->name('app-events-delete');
    Route::post('app-events-bulk-delete', [EventsController::class, 'bulkDelete'])->name('app-events-bluk-destroy');
    //events Type End

    //advisor Type End
    Route::get('advisor/list', [AdvisorController::class, 'index'])->name('app-advisor-list');
    Route::get('advisor/getAll', [AdvisorController::class, 'getAll'])->name('app-advisor-get-all');
    Route::post('advisor/store', [AdvisorController::class, 'store'])->name('app-advisor-store');
    Route::get('advisor/add', [AdvisorController::class, 'create'])->name('app-advisor-add');
    Route::get('advisor/edit/{encrypted_id}', [AdvisorController::class, 'edit'])->name('app-advisor-edit');
    Route::put('advisor/update/{encrypted_id}', [AdvisorController::class, 'update'])->name('app-advisor-update');
    Route::get('advisor/destroy/{encrypted_id}', [AdvisorController::class, 'destroy'])->name('app-advisor-delete');
    Route::get('advisor/{id}', [AdvisorController::class, 'destroyimage'])->name('advisorSliders.destroyimage');

    //advisor Type End

    //admin-user Type End
    Route::get('admin-user/list', [AdminUserController::class, 'index'])->name('app-admin-user-list');
    Route::get('admin-user/getAll', [AdminUserController::class, 'getAll'])->name('app-admin-user-get-all');
    Route::post('admin-user/store', [AdminUserController::class, 'store'])->name('app-admin-user-store');
    Route::get('admin-user/add', [AdminUserController::class, 'create'])->name('app-admin-user-add');
    Route::get('admin-user/edit/{encrypted_id}', [AdminUserController::class, 'edit'])->name('app-admin-user-edit');
    Route::put('admin-user/update/{encrypted_id}', [AdminUserController::class, 'update'])->name('app-admin-user-update');
    Route::get('admin-user/destroy/{encrypted_id}', [AdminUserController::class, 'destroy'])->name('app-admin-user-delete');
    Route::get('admin-user/{id}', [AdminUserController::class, 'destroyimage'])->name('adminuserSliders.destroyimage');

    //admin-user Type End
    //draws Type End
    Route::get('draws/list', [DrawsController::class, 'index'])->name('app-draws-list');
    Route::get('draws/getAll', [DrawsController::class, 'getAll'])->name('app-draws-get-all');
    Route::post('draws/store', [DrawsController::class, 'store'])->name('app-draws-store');
    Route::get('draws/add', [DrawsController::class, 'create'])->name('app-draws-add');
    Route::get('draws/edit/{encrypted_id}', [DrawsController::class, 'edit'])->name('app-draws-edit');
    Route::put('draws/update/{encrypted_id}', [DrawsController::class, 'update'])->name('app-draws-update');
    Route::get('draws/destroy/{encrypted_id}', [DrawsController::class, 'destroy'])->name('app-draws-delete');
    Route::post('app-draws-bulk-delete', [DrawsController::class, 'bulkDelete'])->name('app-draws-bluk-destroy');

    //events Type End

    //application-statuses Type End
    Route::get('application-statuses/list', [ApplicationStatusesController::class, 'index'])->name('app-application-statuses-list');
    Route::get('application-statuses/getAll', [ApplicationStatusesController::class, 'getAll'])->name('app-application-statuses-get-all');
    Route::post('application-statuses/store', [ApplicationStatusesController::class, 'store'])->name('app-application-statuses-store');
    Route::get('application-statuses/add', [ApplicationStatusesController::class, 'create'])->name('app-application-statuses-add');
    Route::get('application-statuses/edit/{encrypted_id}', [ApplicationStatusesController::class, 'edit'])->name('app-application-statuses-edit');
    Route::put('application-statuses/update/{encrypted_id}', [ApplicationStatusesController::class, 'update'])->name('app-application-statuses-update');
    Route::get('application-statuses/destroy/{encrypted_id}', [ApplicationStatusesController::class, 'destroy'])->name('app-application-statuses-delete');
    Route::post('app-application-statuses-bulk-delete', [ApplicationStatusesController::class, 'bulkDelete'])->name('app-application-statuses-destroy');

    //success-stories Type End

    //fcm-tokens Type End
    Route::get('fcm-tokens/list', [FCMTokensController::class, 'index'])->name('app-fcm-tokens-list');
    Route::get('fcm-tokens/getAll', [FCMTokensController::class, 'getAll'])->name('app-fcm-tokens-get-all');
    Route::post('fcm-tokens/store', [FCMTokensController::class, 'store'])->name('app-fcm-tokens-store');
    Route::get('fcm-tokens/add', [FCMTokensController::class, 'create'])->name('app-fcm-tokens-add');
    Route::get('fcm-tokens/edit/{encrypted_id}', [FCMTokensController::class, 'edit'])->name('app-fcm-tokens-edit');
    Route::put('fcm-tokens/update/{encrypted_id}', [FCMTokensController::class, 'update'])->name('app-fcm-tokens-update');
    Route::get('fcm-tokens/destroy/{encrypted_id}', [FCMTokensController::class, 'destroy'])->name('app-fcm-tokens-delete');
    //fcm-tokens Type End

      //default-documents Type End
      Route::get('default-documents/list', [DefaultDocumentsController::class, 'index'])->name('app-default-documents-list');
      Route::get('default-documents/getAll', [DefaultDocumentsController::class, 'getAll'])->name('app-default-documents-get-all');
      Route::post('default-documents/store', [DefaultDocumentsController::class, 'store'])->name('app-default-documents-store');
      Route::get('default-documents/add', [DefaultDocumentsController::class, 'create'])->name('app-default-documents-add');
      Route::get('default-documents/edit/{encrypted_id}', [DefaultDocumentsController::class, 'edit'])->name('app-default-documents-edit');
      Route::put('default-documents/update/{encrypted_id}', [DefaultDocumentsController::class, 'update'])->name('app-default-documents-update');
      Route::get('default-documents/destroy/{encrypted_id}', [DefaultDocumentsController::class, 'destroy'])->name('app-default-documents-delete');
      //default-documents Type End

       //user-documents Type End
       Route::get('user-documents/list', [UserDocumentsController::class, 'index'])->name('app-user-documents-list');
       Route::get('user-documents/getAll', [UserDocumentsController::class, 'getAll'])->name('app-user-documents-get-all');
       Route::post('user-documents/store', [UserDocumentsController::class, 'store'])->name('app-user-documents-store');
       Route::get('user-documents/add', [UserDocumentsController::class, 'create'])->name('app-user-documents-add');
       Route::get('user-documents/edit/{encrypted_id}', [UserDocumentsController::class, 'edit'])->name('app-user-documents-edit');
       Route::put('user-documents/update/{encrypted_id}', [UserDocumentsController::class, 'update'])->name('app-user-documents-update');
       Route::get('user-documents/destroy/{encrypted_id}', [UserDocumentsController::class, 'destroy'])->name('app-user-documents-delete');
       //user-documents Type End

        //uploaded-documents Type End
        Route::get('uploaded-documents/list', [UploadedDocumentsController::class, 'index'])->name('app-uploaded-documents-list');
        Route::get('uploaded-documents/getAll', [UploadedDocumentsController::class, 'getAll'])->name('app-uploaded-documents-get-all');
        Route::post('uploaded-documents/store', [UploadedDocumentsController::class, 'store'])->name('app-uploaded-documents-store');
        Route::get('uploaded-documents/add', [UploadedDocumentsController::class, 'create'])->name('app-uploaded-documents-add');
        Route::get('uploaded-documents/edit/{encrypted_id}', [UploadedDocumentsController::class, 'edit'])->name('app-uploaded-documents-edit');
        Route::put('uploaded-documents/update/{encrypted_id}', [UploadedDocumentsController::class, 'update'])->name('app-uploaded-documents-update');
        Route::get('uploaded-documents/destroy/{encrypted_id}', [UploadedDocumentsController::class, 'destroy'])->name('app-uploaded-documents-delete');
        //user-documents Type End

    //Notification Type End
    Route::get('notifications/list', [NotificationsController::class, 'index'])->name('app-notifications-list');
    Route::get('notifications/getAll', [NotificationsController::class, 'getAll'])->name('app-notifications-get-all');
    Route::post('notifications/store', [NotificationsController::class, 'store'])->name('app-notifications-store');
    Route::get('notifications/add', [NotificationsController::class, 'create'])->name('app-notifications-add');
    Route::get('notifications/edit/{encrypted_id}', [NotificationsController::class, 'edit'])->name('app-notifications-edit');
    Route::put('notifications/update/{encrypted_id}', [NotificationsController::class, 'update'])->name('app-notifications-update');
    Route::get('notifications/destroy/{encrypted_id}', [NotificationsController::class, 'destroy'])->name('app-notifications-delete');
    Route::get('/users-by-client/{clientTypeId}', [NotificationsController::class, 'getUsersByClientType'])
        ->name('users.by.client');
        
    //Notification Type End

    //our-services Type End
    Route::get('our-services/list', [OurServicesController::class, 'index'])->name('app-our-services-list');
    Route::get('our-services/getAll', [OurServicesController::class, 'getAll'])->name('app-our-services-get-all');
    Route::post('our-services/store', [OurServicesController::class, 'store'])->name('app-our-services-store');
    Route::get('our-services/add', [OurServicesController::class, 'create'])->name('app-our-services-add');
    Route::get('our-services/edit/{encrypted_id}', [OurServicesController::class, 'edit'])->name('app-our-services-edit');
    Route::put('our-services/update/{encrypted_id}', [OurServicesController::class, 'update'])->name('app-our-services-update');
    Route::get('our-services/destroy/{encrypted_id}', [OurServicesController::class, 'destroy'])->name('app-our-services-delete');
    Route::post('app-our-services-bulk-delete', [OurServicesController::class, 'bulkDelete'])->name('app-our-services-bluk-destroy');
    //our-services Type End

    //other-stakeholders Type End
    Route::get('other-stakeholders/list', [OtherStakeholdersController::class, 'index'])->name('app-other-stakeholders-list');
    Route::get('other-stakeholders/getAll', [OtherStakeholdersController::class, 'getAll'])->name('app-other-stakeholders-get-all');
    Route::post('other-stakeholders/store', [OtherStakeholdersController::class, 'store'])->name('app-other-stakeholders-store');
    Route::get('other-stakeholders/add', [OtherStakeholdersController::class, 'create'])->name('app-other-stakeholders-add');
    Route::get('other-stakeholders/edit/{encrypted_id}', [OtherStakeholdersController::class, 'edit'])->name('app-other-stakeholders-edit');
    Route::put('other-stakeholders/update/{encrypted_id}', [OtherStakeholdersController::class, 'update'])->name('app-other-stakeholders-update');
    Route::get('other-stakeholders/destroy/{encrypted_id}', [OtherStakeholdersController::class, 'destroy'])->name('app-other-stakeholders-delete');
    Route::get('/get-users', [OtherStakeholdersController::class, 'getUsers'])->name('get.users');
    Route::post('app-other-stakeholders-bulk-delete', [OtherStakeholdersController::class, 'bulkDelete'])->name('app-other-stakeholders-bluk-destroy');


    //other-stakeholders Type End

    //news Type End
    Route::get('news/list', [NewsController::class, 'index'])->name('app-news-list');
    Route::get('news/getAll', [NewsController::class, 'getAll'])->name('app-news-get-all');
    Route::post('news/store', [NewsController::class, 'store'])->name('app-news-store');
    Route::get('news/add', [NewsController::class, 'create'])->name('app-news-add');
    Route::get('news/edit/{encrypted_id}', [NewsController::class, 'edit'])->name('app-news-edit');
    Route::put('news/update/{encrypted_id}', [NewsController::class, 'update'])->name('app-news-update');
    Route::get('news/destroy/{encrypted_id}', [NewsController::class, 'destroy'])->name('app-news-delete');
    Route::get('news/{encrypted_id}', [NewsController::class, 'destroyimage'])->name('news.destroyimage');
    Route::post('app-news-bulk-delete', [NewsController::class, 'bulkDelete'])->name('app-news-bluk-destroy');

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
    Route::post('app-faq-categories-bulk-delete', [FaqCategoryController::class, 'bulkDelete'])->name('app-faq-categories-bluk-destroy');

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
    Route::post('app-faq-bulk-delete', [FaqController::class, 'bulkDelete'])->name('app-faq-bluk-destroy');

    //faq Type End


    //internal-program-statuses Type End
    Route::get('internal-program-statuses/list', [InternalProgramStatusController::class, 'index'])->name('app-internal-program-statuses-list');
    Route::get('internal-program-statuses/getAll', [InternalProgramStatusController::class, 'getAll'])->name('app-internal-program-statuses-get-all');
    Route::post('internal-program-statuses/store', [InternalProgramStatusController::class, 'store'])->name('app-internal-program-statuses-store');
    Route::get('internal-program-statuses/add', [InternalProgramStatusController::class, 'create'])->name('app-internal-program-statuses-add');
    Route::get('internal-program-statuses/edit/{encrypted_id}', [InternalProgramStatusController::class, 'edit'])->name('app-internal-program-statuses-edit');
    Route::put('internal-program-statuses/update/{encrypted_id}', [InternalProgramStatusController::class, 'update'])->name('app-internal-program-statuses-update');
    Route::get('internal-program-statuses/destroy/{encrypted_id}', [InternalProgramStatusController::class, 'destroy'])->name('app-internal-program-statuses-delete');
    //internal-program-statuses Type End

    //home-services Type End
    Route::get('home-services/list', [HomeServiceController::class, 'index'])->name('app-home-services-list');
    Route::get('home-services/getAll', [HomeServiceController::class, 'getAll'])->name('app-home-services-get-all');
    Route::post('home-services/store', [HomeServiceController::class, 'store'])->name('app-home-services-store');
    Route::get('home-services/add', [HomeServiceController::class, 'create'])->name('app-home-services-add');
    Route::get('home-services/edit/{encrypted_id}', [HomeServiceController::class, 'edit'])->name('app-home-services-edit');
    Route::put('home-services/update/{encrypted_id}', [HomeServiceController::class, 'update'])->name('app-home-services-update');
    Route::get('home-services/destroy/{encrypted_id}', [HomeServiceController::class, 'destroy'])->name('app-home-services-delete');
    Route::get('home-services-icon/{encrypted_id}', [HomeServiceController::class, 'destroyimageServiceIcon'])->name('faq.destroyimageServiceIcon');
    Route::get('home-services-image/{encrypted_id}', [HomeServiceController::class, 'destroyimageServiceImage'])->name('faq.destroyimageServiceImage');

    //home-services Type End




    //report End
    Route::get('status-chart-report', [DashboardController::class, 'index'])->name('app-status-chart-report');
    Route::get('/get-users', [DashboardController::class, 'getUsers_data'])->name('getUsers.list');
    Route::get('/get-application-statuses', [DashboardController::class, 'getApplicationStatuses'])->name('getApplicationStatuses.list');
    Route::get('/get-data-users', [DashboardController::class, 'getUsersData'])->name('get-data-users');
    Route::get('/admin/get-summry', [DashboardController::class, 'getSummry'])->name('admin/get-summry');

    //report End

});
/* Route Apps */
