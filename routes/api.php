<?php

use App\Http\Controllers\Admin\NotificationPopupController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\SupportRequestController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\User\VerifyEmailController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ServiceRuleController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\GeneralPloblemController;
use App\Http\Controllers\User\FooterController;
use App\Http\Controllers\PaymentGuideController;
use App\Http\Controllers\PolicyRegulationsController;
use App\Http\Controllers\PrivacyPolicyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['api']], function () {
    Route::group(['prefix' => 'admins', 'middleware' => ['assign.guard:admins']], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('change-password', [AuthController::class, 'updateAuthPassword']);
            Route::get('me', [AuthController::class, 'me']);
        });

        Route::group(['prefix' => 'employees'], function () {
            Route::get('/', [EmployeeController::class, 'index']);
            Route::post('/{id}', [EmployeeController::class, 'update']);
            Route::delete('/{id}', [EmployeeController::class, 'destroy']);
            Route::put('/{id}', [EmployeeController::class, 'changePassword']);
            Route::post('/', [EmployeeController::class, 'store']);
        });

        Route::group(['middleware' => ['jwt.auth']], function () {
            Route::group(['prefix' => '/roles'], function () {
                Route::get('/',[Admin\RoleController::class,'index'])->middleware('checkPermission:get-roles');
                Route::get('/all',[Admin\RoleController::class,'getAllRoles']);
                Route::get('/{id}',[Admin\RoleController::class,'show'])->middleware('checkPermission:get-role-detail');
                Route::post('/',[Admin\RoleController::class,'store'])->middleware('checkPermission:create-role');
                Route::put('/{id}',[Admin\RoleController::class,'update'])->middleware('checkPermission:update-role');
                Route::delete('/{id}',[Admin\RoleController::class,'destroy'])->middleware('checkPermission:delete-role');
                Route::post('/add-permission/{id}',[Admin\RoleController::class,'addPermissionForRole'])->middleware('checkPermission:update-permissions-for-role');
                Route::post('/remove-permission/{id}',[Admin\RoleController::class,'removePermissionForRole'])->middleware('checkPermission:update-permissions-for-role');
            });

            Route::group(['prefix' => '/support-request'], function () {
                Route::get('/',[SupportRequestController::class,'index']);
                Route::get('/amount',[SupportRequestController::class,'amountRequest']);
                Route::post('/{id}',[SupportRequestController::class,'changeStatus']);
            });

            Route::group(['prefix' => '/orders'], function () {
                Route::get('/',[OrderController::class,'index']);
                Route::get('/not-quote',[OrderController::class,'getOrderNotQuote']);
                Route::put('/',[OrderController::class,'update']);
                Route::get('/count-order', [OrderController::class, 'countOrder']);
                Route::get('/get-revenue', [OrderController::class, 'getTotalRevenue']);
                Route::get('/statistic-order', [OrderController::class, 'orderStatistics']);
            });

            Route::group(['prefix' => '/permissions'], function () {
                Route::get('/',[Admin\PermissionController::class,'index'])->middleware('checkPermission:get-permissions');
            });

            Route::group(['prefix' => 'feedbacks'], function () {
                Route::get('/', [FeedbackController::class, 'index']);
            });

            Route::group(['prefix' => 'users'], function () {
                Route::get('/', [Admin\UserController::class, 'index']);
                Route::get('/count-user', [Admin\UserController::class, 'countUser']);

            });

            Route::group(['prefix' => 'payment'], function () {
                Route::get('/vnpay', [Admin\PaymentController::class, 'listPaymentVnpay']);
                Route::get('/momo', [Admin\PaymentController::class, 'listPaymentMomo']);
            });

            Route::post('upload-document', [DocumentController::class, 'adminUpload']);

            Route::group(['prefix' => 'service-rule'], function () {
                Route::post('', [ServiceRuleController::class, 'store']);
            });

            Route::group(['prefix' => 'bank'], function () {
                Route::get('/',[BankController::class,'index']);
                Route::post('/', [BankController::class, 'store']);
                Route::post('/update-bank', [BankController::class, 'handleCreateAndUpdateBank']);
                Route::post('/{id}',[BankController::class,'update']);
                Route::delete('/{id}', [BankController::class, 'destroy']);
                Route::get('/get-data', [BankController::class, 'getBankAccount']);
            });

            Route::group(['prefix' => 'communication'], function () {
                Route::post('/', [CommunicationController::class, 'handleCreateAndUpdateCommunication']);
                Route::get('/get-data', [CommunicationController::class, 'getCommunation']);
            });

            Route::group(['prefix' => 'language'], function () {
                Route::get('/', [Admin\LanguageController::class, 'index']);
                Route::post('/',[Admin\LanguageController::class,'store']);
                Route::put('/{id}',[Admin\LanguageController::class,'update']);
                Route::delete('/{id}',[Admin\LanguageController::class,'destroy']);
            });

            Route::group(['prefix' => 'members'], function () {
                Route::get('/', [MemberController::class, 'index']);
                Route::post('/', [MemberController::class, 'store']);
                Route::post('/{id}',[MemberController::class,'update']);
                Route::delete('/{id}',[MemberController::class,'destroy']);
            });


            Route::group(['prefix' => 'emails'], function () {
                Route::get('/', [EmailController::class, 'index']);
                Route::get('/{id}', [EmailController::class, 'show']);
                Route::post('/{id}',[EmailController::class,'update']);
            });


            Route::group(['prefix' => 'footers'], function () {
                Route::get('/get-data', [Admin\FooterController::class, 'getFooterInfo']);
                Route::post('/', [Admin\FooterController::class, 'handleCreateAndUpdateFooter']);
            });


            Route::group(['prefix' => 'price-list'],function(){
                Route::get('/',[Admin\PriceListController::class,'index']);
                Route::post('/store',[Admin\PriceListController::class,'store']);
            });

            Route::group(['prefix' => 'categories'],function(){
                Route::get('/',[Admin\CategoryController::class,'index']);
                Route::post('/',[Admin\CategoryController::class,'store']);
                Route::put('/{id}',[Admin\CategoryController::class,'update']);
                Route::delete('/{id}',[Admin\CategoryController::class,'destroy']);
            });
        });
    });

    Route::group(['prefix' => 'users', 'middleware' => ['assign.guard:users']], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('login', [AuthController::class, 'userLogin']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('register', [AuthController::class, 'userRegister']);
            Route::post('change-password', [AuthController::class, 'updateAuthPassword']);
            Route::get('me', [AuthController::class, 'me']);

            Route::get('/login-google', [AuthController::class, 'redirectToGoogle']);
            Route::POST('/login-google/callback', [AuthController::class, 'loginGoogleCallback']);

            Route::get('/login-facebook', [AuthController::class, 'redirectToFacebook']);
            Route::POST('/login-facebook/callback', [AuthController::class, 'loginFacebookCallback']);

            Route::POST('/login-with-sosial-network', [AuthController::class, 'handleLoginWithSosialSDK']);
        });

        Route::group(['middleware' => ['jwt.auth']], function () {
            Route::group(['prefix' => 'payments'], function () {
                Route::post('/', [PaymentController::class, 'store']);
                Route::post('/payment-success', [PaymentController::class, 'paymentSuccess']);
                Route::post('/payment-momo', [PaymentController::class, 'paymentMomo']);
                Route::post('/payment-cash', [PaymentController::class, 'handleCashPayment']);
                Route::post('/payment-paypal', [PaymentController::class, 'handlePaypalPayment']);
            });
        });

        Route::post('/payment-momo-confirm', [PaymentController::class, 'paymentMomoConfirm']);

        Route::group(['prefix' => 'feedbacks'], function () {
            Route::post('/send-feedback', [FeedbackController::class, 'store']);
            Route::post('/update-feedback', [FeedbackController::class, 'update']);
            Route::get('/', [FeedbackController::class, 'getListFeedbacks']);
        });

        Route::group(['prefix' => 'bank'], function () {
            Route::get('/get-data', [BankController::class, 'getBankAccount']);
        });

        Route::group(['prefix' => 'communication'], function () {
            Route::get('/get-data', [CommunicationController::class, 'getCommunation']);
        });

        Route::group(['prefix' => 'members'], function () {
            Route::get('/get-all', [MemberController::class, 'getAllMembers']);
        });


        Route::group(['prefix' => 'notification-popup'], function () {
            Route::get('/get-notification-group', [\App\Http\Controllers\User\NotificationPopupController::class, 'getNotificationByGroup']);
        });

        Route::post('/update-information', [UserController::class, 'updateInfo']);
        Route::post('/update-email', [UserController::class, 'updateEmail']);
        Route::post('/update-phone-number', [UserController::class, 'updatePhoneNumber']);
        Route::post('/update-password', [UserController::class, 'updateAuthPassword']);
        Route::post('check-password', [UserController::class, 'checkPassword']);
        Route::post('verify-email', [VerifyEmailController::class, 'verifyEmail']);
        Route::post('result-verify-email', [VerifyEmailController::class, 'resultVerifyEmail']);

        Route::group(['prefix' => 'footers'], function () {
            Route::get('/', [FooterController::class, 'index']);
        });

        Route::get('/price-list',[Admin\PriceListController::class,'index']);
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/feedback', [UserController::class, 'feedback']);
        Route::post('/list-order-payment', [UserController::class, 'getListOrderPayment']);
        Route::post('/list-order', [UserController::class, 'getListOrder']);
        Route::get('/list-order/{id}', [UserController::class, 'getListOrderForUser']);
    });

    Route::post('upload-document', [DocumentController::class, 'userUpload']);
    Route::post('update-document', [DocumentController::class, 'updateDocument']);
    Route::delete('delete-order/{id}', [OrderController::class, 'destroy']);
    Route::get('download-document/{id}', [DocumentController::class, 'download']);
    Route::get('service-rule', [ServiceRuleController::class, 'index']);

});

Route::post('support-request', [SupportRequestController::class, 'store']);
Route::post('forgot-password', [UserController::class, 'forgotPassword']);
Route::get('download-file/{id}', [DocumentController::class, 'downloadFile']);
