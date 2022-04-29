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
use App\Http\Controllers\ReceptionistAudioController;
use App\Http\Controllers\UserReceptionistController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\PelayananController;
use App\Http\Controllers\ReportController;

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
Route::get('layar/{id}',[UserReceptionistController::class,'layar']);
Route::get('all-layar',[UserReceptionistController::class,'all_layar']);
Route::get('antrian/{id}/pdf',[AntrianController::class,'antrian_pdf']);

Route::get('timeframe',[ReportController::class,'timeframe']);


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
    Route::get('keperluan-all',[KeperluanController::class,'all']);
    Route::resource('receptionist',ReceptionistController::class);

    Route::post('signin-receptionist',[UserReceptionistController::class,'store']);
    Route::post('signout-receptionist',[UserReceptionistController::class,'signout_receptionist']);
    Route::get('get-receptionist',[UserReceptionistController::class,'get_receptionist']);
    Route::get('available-receptionist',[UserReceptionistController::class,'available_receptionist']);
    Route::get('list-antrian-by-user-receptionist/{id}',[UserReceptionistController::class,'list_antrian_by_user_receptionist']);
    Route::post('change-antrian/{id}',[UserReceptionistController::class,'change_antrian']); 

    Route::resource('pelayanan',PelayananController::class);
    Route::post('selesai-pelayanan',[PelayananController::class,'store']); 

    Route::get('antrian',[AntrianController::class,'index']);
    Route::get('kategori-antrian',[ReportController::class,'kategori_antrian']);
    Route::get('report-keperluan',[ReportController::class,'report_keperluan']);

    Route::resource('receptionist-audio',ReceptionistAudioController::class);
    Route::get('audio-by-receptionist/{id}',[ReceptionistAudioController::class,'by_receptionist']);
});