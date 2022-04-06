<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Auth\RegistrasiRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\RecoveryPasswordRequest;
use App\Services\GuestService;
use App\Transformers\UserTransformer;
use Laravel\Socialite\Facades\Socialite;

class GuestController extends Controller
{
    public function registrasi(RegistrasiRequest $registrasiRequest, GuestService $guestService)
    {
        $model = $guestService->registrasi($registrasiRequest->all());

        $response = fractal($model, new UserTransformer())
            ->toArray();

        return response()->json($response, 201);
    }

    public function verifikasi(GuestService $guestService, $id)
    {
        $model = $guestService->verifikasi_email($id);

        return response()->json($model, 200);
    }

    public function login(LoginRequest $loginRequest, GuestService $guestService)
    {
        $source = $loginRequest->input('source');

        if($source == "login")
        {
            $model = $guestService->login($loginRequest->all());
        }else if($source == "social")
        {
            $token = $loginRequest->input('username');
            
            $model = array( 
                'success'=>true,
                'message'=>'Login successfully',
                'errors'=>array(),
                'access_token'=>$token,
                'token_type'=>'Bearer'
            );
        }
        

        return response()->json($model, 200);
    }

    public function forgot_password(ForgotPasswordRequest $forgotPasswordRequest, GuestService $guestService)
    {
        $model = $guestService->forgot_password($forgotPasswordRequest);

        return response()->json($model, 200);
    }

    public function recovery_password(RecoveryPasswordRequest $recoveryPasswordRequest, GuestService $guestService)
    {
        $model = $guestService->password_recovery($recoveryPasswordRequest);

        return response()->json($model, 200);
    }
    
}
