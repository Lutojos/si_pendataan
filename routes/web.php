<?php

use App\Http\Controllers\V1\Admin\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::controller(AuthController::class)->name('auth.')->group(function () {
    Route::match(['get', 'post'], '/', 'index')->name('form')->middleware('guest');
    Route::get('/login', function () {
        return redirect()->route('auth.form');
    });
    Route::post('/login', 'login')->name('login');
});

Route::controller(ForgotPasswordController::class)->name('reset.password.')->prefix('/reset-password')->group(function () {
    Route::get('/with-token/{token}', 'showResetPasswordForm')->name('get');
    Route::post('/submit', 'submitResetPasswordForm')->name('post');
    Route::get('/', 'index')->name('form');
    Route::post('/forget-password', 'submitForgetPasswordForm')->name('forget');
});

Route::group(['middleware' => ['auth', 'xss']], function () {
    // auth
    Route::controller(AuthController::class)->name('auth.')->group(function () {
        Route::post('/logout', 'logout')->name('logout');
    });
    // dashboard
    Route::controller(DashboardController::class)->name('dashboard.')->group(function () {
        Route::get('/dashboard', 'index')->name('index');
    });

    // Provinsi
    Route::controller(ProvinsiController::class)->name('provinsi.')->prefix('master-data/provinsi')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/list', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{token?}', 'edit')->name('edit');
        Route::post('/update/{token}', 'update')->name('update');
        Route::get('/delete/{token?}', 'delete')->name('delete');
        Route::get('/option}', 'option')->name('option');
    });

    Route::controller(KotaController::class)->name('kota.')->prefix('master-data/kota')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/list', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{token?}', 'edit')->name('edit');
        Route::post('/update/{token}', 'update')->name('update');
        Route::get('/delete/{token?}', 'delete')->name('delete');
        Route::get('/option}', 'option')->name('option');
    });

    Route::controller(KecamatanController::class)->name('kecamatan.')->prefix('master-data/kecamatan')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/list', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{token?}', 'edit')->name('edit');
        Route::post('/update/{token}', 'update')->name('update');
        Route::get('/delete/{token?}', 'delete')->name('delete');
        Route::get('/option}', 'option')->name('option');
    });

    Route::controller(DesaController::class)->name('desa.')->prefix('master-data/desa')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/list', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{token?}', 'edit')->name('edit');
        Route::post('/update/{token}', 'update')->name('update');
        Route::get('/delete/{token?}', 'delete')->name('delete');
        Route::get('/option}', 'option')->name('option');
    });

    Route::controller(AnggotaController::class)->name('anggota.')->prefix('anggota')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/list', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{token?}', 'edit')->name('edit');
        Route::post('/update/{token}', 'update')->name('update');
        Route::get('/delete/{token?}', 'delete')->name('delete');
        Route::get('/option}', 'option')->name('option');
    });

    // role
    Route::controller(RoleController::class)->name('role.')->prefix('management-user/role')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/list', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{token?}', 'edit')->name('edit');
        Route::post('/update/{token}', 'update')->name('update');
        Route::get('/delete/{token?}', 'delete')->name('delete');

        // permission
        Route::controller(PermissionController::class)->name('permission.')->prefix('/permission')->group(function () {
            Route::get('/{token?}', 'index')->name('index');
            Route::post('/', 'assign')->name('assign');
        });
    });

    // user
    Route::controller(UserController::class)->name('user.')->prefix('management-user/user')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/list', 'list')->name('list');
        //detail
        Route::get('/detail/{token?}', 'show')->name('detail');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{token?}', 'edit')->name('edit');
        Route::post('/{token}/update', 'update')->name('update');
        Route::delete('/delete/{token?}', 'delete')->name('delete');
        Route::get('/option', 'option')->name('option');
    });
});
