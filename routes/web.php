<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    AddressController,
    ServiceController,
    ManagerController,
    AvailabilityController,
    BookingController,
    HolidayController,
    ProfileController,
    EmailController,
};

Route::view('/', 'welcome');

Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
Route::post('/booking', [BookingController::class, 'booking'])->name('booking.store');

Route::prefix('api')->group(function () {
    Route::get('/booking/available', [BookingController::class, 'getAvailableTimes'])->name('getAvailableTimes');
    Route::get('/users-by-address', [BookingController::class, 'getStylistsByAddress'])->name('getStylistsByAddress');
    Route::get('/holidays', [HolidayController::class, 'getAll'])->name('getAllHolidays');
});

Route::post('/email/send-code', [EmailController::class, 'sendCode'])
    ->middleware('throttle:3,1');
Route::post('/email/verify-otp', [EmailController::class, 'verifyCode']);
Route::post('/email/reset-otp', [EmailController::class, 'resetOtp']);


Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/login', [UserController::class, 'loginPost'])->name('login.post');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/activate', [UserController::class, 'activate'])->name('activate');
Route::post('/activate', [UserController::class, 'activatePost'])->name('activate.post');

Route::middleware('auth')
    ->prefix('dashboard')
    ->group(function () {

        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::middleware('role:owner')->group(function () {
            Route::resources([
                'addresses' => AddressController::class,
                'services'  => ServiceController::class,
                'holidays'  => HolidayController::class,
            ]);
        });

        Route::get('/availability', [AvailabilityController::class, 'index'])
            ->name('availabilities.index');
        Route::post('/availability/{user}', [AvailabilityController::class, 'store'])
            ->name('availabilities.store');

        Route::get('/manager', [ManagerController::class, 'index'])
            ->name('managers.index');
        Route::get('/manager/{booking}/edit', [ManagerController::class, 'edit'])
            ->name('managers.edit');
        Route::put('/manager/{booking}', [ManagerController::class, 'update'])
            ->name('managers.update');
        Route::patch('/manager/{booking}/status', [ManagerController::class, 'updateStatus'])
            ->name('managers.updateStatus');

        Route::middleware('role:owner,manager')
            ->prefix('users')
            ->group(function () {
                Route::get('/create', [UserController::class, 'create'])->name('users.create');
                Route::post('/', [UserController::class, 'store'])->name('users.store');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
                Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            });
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
