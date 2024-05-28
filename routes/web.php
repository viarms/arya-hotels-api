<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    Route::get('/status', function (Request $request) {
        if (!$request->user('sanctum')) {
            return response()->json([
                'status' => 'unauthenticated',
                'user' => null,
            ]);
        }

        return response()->json([
            'status' => 'authenticated',
            'user' => $request->user(),
        ]);
    });

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('login');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});

Route::prefix('destination')->group(function () {
    Route::post('/upload-file', [DestinationController::class, 'storeFile'])->middleware('auth:sanctum');
    Route::post('/', [DestinationController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/', [DestinationController::class, 'getAll']);
    Route::get('/one', [DestinationController::class, 'getOne']);
    Route::get('/one/{slug}', [DestinationController::class, 'getOne']);
    Route::delete('/one/{id}', [DestinationController::class, 'removeOne'])->middleware('auth:sanctum');
    Route::post('/copy/{id}', [DestinationController::class, 'copyOne'])->middleware('auth:sanctum');
});

Route::prefix('hotel')->group(function () {
    Route::post('/upload-file', [HotelController::class, 'storeFile'])->middleware('auth:sanctum');
    Route::post('/', [HotelController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/', [HotelController::class, 'getAll']);
    Route::get('/one', [HotelController::class, 'getOne']);
    Route::get('/one/{slug}', [HotelController::class, 'getOne']);
    Route::delete('/one/{id}', [HotelController::class, 'removeOne'])->middleware('auth:sanctum');
    Route::post('/copy/{id}', [HotelController::class, 'copyOne'])->middleware('auth:sanctum');
});


Route::prefix('contact')->group(function () {
    Route::post('/', [ContactController::class, 'store']);
    Route::get('/', [ContactController::class, 'getAll']);
    Route::post('/mark-as-done', [ContactController::class, 'markAsDone']);
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
