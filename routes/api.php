<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\V1\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V1\Auth\UserController;
use App\Http\Controllers\Api\V1\AdvertisementsController;
use App\Http\Controllers\Api\V1\AppointmentsController;
use App\Http\Controllers\Api\V1\AvailabilityController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\CategoriesController;
use App\Http\Controllers\Api\V1\DoctorsController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\FavoritesController;
use App\Http\Controllers\Api\V1\MedicalReportsController;
use App\Http\Controllers\Api\V1\MessagesController;
use App\Http\Controllers\Api\V1\PaymentsController;
use App\Http\Controllers\Api\V1\PromoCodesController;
use App\Http\Controllers\Api\V1\ReviewsController;
use App\Http\Controllers\Api\V1\TreatmentsController;
use App\Http\Controllers\Api\V1\TimeSlotsController;

use App\Http\Middleware\IsAdmin;
use App\Services\AgoraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgetpassword', [ForgetPasswordController::class, 'forgetPassword']);
Route::post('resetpassword', [ResetPasswordController::class, 'resetpassword']);
Route::post('otp/generate', [AuthController::class, 'generateOpt']);
Route::post('verify', [AuthController::class, 'verify']);
Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('user', [UserController::class, 'user'])->middleware('auth:api');
Route::post('/uploadProfilePic', [UserController::class, 'uploadProfilePic'])->middleware('auth:api');

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoriesController::class, 'index']);
    Route::get('{id}', [CategoriesController::class, 'show']);
    Route::get('slug/{slug}', [CategoriesController::class, 'getCategoryBySlug']);

    Route::middleware([IsAdmin::class])->group(function () {
        Route::post('/', [CategoriesController::class, 'store']);
        Route::put('{id}', [CategoriesController::class, 'update']);
        Route::delete('{id}', [CategoriesController::class, 'destroy']);
    });
});
Route::prefix('faqs')->group(function () {
    Route::get('/', [FaqController::class, 'index']);
    Route::get('{id}', [FaqController::class, 'show']);
    Route::middleware([IsAdmin::class])->group(function () {
        Route::post('/', [FaqController::class, 'store']);
        Route::put('{id}', [FaqController::class, 'update']);
        Route::delete('{id}', [FaqController::class, 'destroy']);
    });
});



Route::prefix('treatments')->group(function () {
    Route::get('/', [TreatmentsController::class, 'index']);
    Route::get('/top-rated', [TreatmentsController::class, 'topRated']);
    Route::get('/home-treatments', [TreatmentsController::class, 'homeBased']);
    Route::get('/search/{categoryId}', [TreatmentsController::class, 'getTreatmentByCategory']);
    Route::get('{id}/discounted-price', [TreatmentsController::class, 'getDiscountedPrice']);
    Route::get('{id}', [TreatmentsController::class, 'show']);
    Route::middleware([IsAdmin::class])->group(function () {
        Route::post('/', [TreatmentsController::class, 'store']);
        Route::put('{id}', [TreatmentsController::class, 'update']);
        Route::delete('{id}', [TreatmentsController::class, 'destroy']);
    });
});

Route::prefix('offers')->group(function () {
    Route::get('/', [AdvertisementsController::class, 'index']);
    Route::post('/', [AdvertisementsController::class, 'store']);
    Route::get('{id}', [AdvertisementsController::class, 'show']);
    Route::put('{id}', [AdvertisementsController::class, 'update']);
    Route::delete('{id}', [AdvertisementsController::class, 'destroy']);
});


Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'index']);
    Route::get('{id}', [BlogController::class, 'show']);
    Route::get('slug/{slug}', [BlogController::class, 'getBlogBySlug']);
    Route::middleware([IsAdmin::class])->group(function () {
        Route::post('/', [BlogController::class, 'store']);
        Route::put('{id}', [BlogController::class, 'update']);
        Route::delete('{id}', [BlogController::class, 'destroy']);
    });
});





Route::middleware('auth:api')->group(function () {

    // Route::middleware(['admin', IsAdmin::class])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('admin', IsAdmin::class);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store'])->middleware('admin', IsAdmin::class);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('admin', IsAdmin::class);
    });
    // });

    Route::prefix('doctors')->group(function () {
        Route::get('/', [DoctorsController::class, 'index']);
        Route::get('/{id}', [DoctorsController::class, 'show']);
        Route::middleware(['admin', IsAdmin::class])->group(function () {
            Route::post('/', [DoctorsController::class, 'store']);
            Route::put('/{id}', [DoctorsController::class, 'update']);
            Route::delete('/{id}', [DoctorsController::class, 'destroy']);
        });
    });

    // Route::resource('doctors', DoctorsController::class)->middleware([IsAdmin::class]);





    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentsController::class, 'index']);
        Route::get('/user-appointments/{user_id}', [AppointmentsController::class, 'userAppointments']);
        Route::post('/store', [AppointmentsController::class, 'store']);
        Route::get('{id}', [AppointmentsController::class, 'show']);
        Route::put('{id}', [AppointmentsController::class, 'update']);
        Route::delete('{id}', [AppointmentsController::class, 'destroy']);
    });

    Route::prefix('favorites')->group(function () {
        Route::post('/all', [FavoritesController::class, 'index']);
        Route::post('/', [FavoritesController::class, 'store']);
        Route::delete('/', [FavoritesController::class, 'destroy']);
        Route::post('/check', [FavoritesController::class, 'isFavorited']);
    });

    Route::prefix('messages')->group(function () {
        Route::get('/', [MessagesController::class, 'index']);
        Route::get('/user-messages', [MessagesController::class, 'userMessages']);
        Route::get('/sender-receiver-messages', [MessagesController::class, 'senderAndReceiverMessages']);
        Route::post('/', [MessagesController::class, 'store']);
        Route::get('{id}', [MessagesController::class, 'show']);
        Route::put('{id}', [MessagesController::class, 'update']);
        Route::delete('{id}', [MessagesController::class, 'destroy']);
    });

    Route::prefix('medical-reports')->group(function () {
        Route::get('/', [MedicalReportsController::class, 'index']);
        Route::get('/user-medical-reports', [MedicalReportsController::class, 'userMedicalReports']);
        Route::get('/{id}', [MedicalReportsController::class, 'show']);
        Route::post('/', [MedicalReportsController::class, 'store']);
        Route::put('/{id}', [MedicalReportsController::class, 'update']);
        Route::delete('/{id}', [MedicalReportsController::class, 'destroy']);
    });


    Route::prefix('payments')->group(function () {
        Route::post('/card', [PaymentsController::class, 'processCardPayment']);
        Route::post('/cash', [PaymentsController::class, 'processCashPayment']);
        Route::post('/apple-pay', [PaymentsController::class, 'createApplePayIntent']);
        Route::post('/update-status', [PaymentsController::class, 'updatePaymentStatus']);
    });

    Route::get('/agora/token', function (Request $request) {
        $channelName = $request->get('channel_name');
        $uid = $request->get('uid', 0); // Use 0 for dynamic user ID
        $role = $request->get('role', AgoraService::ROLE_SUBSCRIBER);
        $expireTimeInSeconds = $request->get('expiry', 3600);

        $token = AgoraService::generateToken($channelName, $uid, $role, $expireTimeInSeconds);

        return response()->json(['token' => $token]);
    });
});


// Route::resource('medical-reports', MedicalReportsController::class);



// Route::apiResource('appointments', AppointmentsController::class);
Route::apiResource('reviews', ReviewsController::class);
// Route::apiResource('favorites', FavoritesController::class);
// Route::delete('favorites/remove', [FavoritesController::class, 'destroy']);






Route::post('/promo-code/validate', [PromoCodesController::class, 'validatePromoCode']);
Route::post('/promo-code/apply', [PromoCodesController::class, 'applyPromoCode']);

// Route::apiResource('timeslots', TimeSlotsController::class);
// Route::post('timeslots/{id}', [TimeSlotsController::class, 'updateTimeSlotAvailablty']);



Route::prefix('timeslots')->group(function () {
    Route::get('/', [TimeSlotsController::class, 'index']);
    Route::post('/', [TimeSlotsController::class, 'store']);
    Route::get('{id}', [TimeSlotsController::class, 'show']);
    Route::put('{id}', [TimeSlotsController::class, 'update']);
    Route::delete('{id}', [TimeSlotsController::class, 'destroy']);
    Route::post('/update-availablty/{id}', [TimeSlotsController::class, 'updateTimeSlotAvailablty']);
});
Route::post('/getDoctorAvailableSlot', [TimeSlotsController::class, 'getDoctorAvailableSlot']);





