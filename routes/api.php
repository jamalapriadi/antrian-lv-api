<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\PermissionController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\KeperluanController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\UserReceptionistController;
use App\Http\Controllers\AntrianController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [GuestController::class,'login']);
Route::post('password/forgot', [GuestController::class,'forgot_password']);
Route::post('password/recovery',[GuestController::class,'recovery_password']);

Route::get('list-keperluan',[KeperluanController::class,'list_keperluan']);
Route::post('buat-antrian',[AntrianController::class,'simpan_antrian']);

Route::group(['prefix' => 'auth', 'middleware' => 'auth:sanctum'], function() {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('refresh',[AuthController::class,'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logoutall', [AuthController::class,'logoutall']);
    Route::post('change-password', [AuthController::class, 'change_password']);
    Route::post('update-foto', [AuthController::class, 'update_foto']);
    Route::post('update-info', [AuthController::class, 'update_info']);
    Route::post('update-password',[AuthController::class,'update_password']);

    Route::resource('permission',PermissionController::class);
    Route::get('all-permission',[PermissionController::class,'list_permission']);
    Route::resource('role',RoleController::class);
    Route::get('all-role',[RoleController::class,'list_role']);
    Route::post('set-role-permission/{id}',[RoleController::class,'save_role_permission']);
    Route::resource('user',UserController::class);
    Route::post('set-user-roles/{id}',[UserController::class,'update_role_user']);
    Route::post('status-user/{id}',[UserController::class,'status_user']);
    Route::get('all-user',[UserController::class,'all']);

    Route::resource('keperluan',KeperluanController::class);
    Route::resource('receptionist',ReceptionistController::class);

    Route::post('signin-receptionist',[UserReceptionistController::class,'store']);
    Route::get('get-receptionist',[UserReceptionistController::class,'get_receptionist']);
    Route::get('available-receptionist',[UserReceptionistController::class,'available_receptionist']);
});