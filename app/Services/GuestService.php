<?php 

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrasiEmail;
use App\Mail\ForgotPassword;

class GuestService 
{
    public function registrasi($data)
    {
        $model = User::create(
            [
                'name'=>$data['nama'],
                'email'=>$data['email'],
                'password'=>Hash::make($data['password']),
                'active'=>'N',
                'remember_token'=>Str::random(40) . $data['email'],
            ]
        );

        $receiver = $model->email;
        $full_name = $model->name;
        $link_activation = env('FE_URL').'/activation/'.$model->remember_token;

        Mail::to($receiver)->send(new RegistrasiEmail($full_name, $link_activation));

        return $model;

    }

    public function login($data)
    {
        $cek_exists = $this->cek_exists_user($data['username']);
        if($cek_exists)
        {
            if(Hash::check($data['password'], $cek_exists->password) == true)
            {
                $tokenResult = $cek_exists->createToken('token-auth')->plainTextToken;
                $data= array( 
                    'success'=>true,
                    'message'=>'Login successfully',
                    'errors'=>array(),
                    'access_token'=>$tokenResult,
                    'token_type'=>'Bearer'
                );
            }else{
                $data = array( 
                    'success'=>false,
                    'message'=>'Password didnt match',
                    'errors'=>array()
                );
            }

            
        }else{
            $data = array( 
                'success'=>false,
                'message'=>'User Not Found',
                'errors'=>array()
            );
        }

        return $data;
    }

    public function cek_exists_user($username)
    {
        $model = User::where(function($q) use($username){
            $q->where('email', $username);
        })->where('active','Y')
        ->first();

        return $model;
    }

    public function verifikasi_email($id)
    {
        $user = User::where('remember_token', $id)
            ->where('active','N')
            ->first();

        if($user){
            $ac = User::find($user->id);
            $ac->active = 'Y';
            $ac->email_verified_at = date('Y-m-d H:i:s');
            $ac->remember_token = Str::random(40) . $user->email;
            $ac->save();


            $data = array( 
                'success'=>true,
                'message'=>'Your Account has been Activated, <br> Now you can Login using your <strong>Username / Email</strong>',
            );
        }else{
            $data = array( 
                'success'=>false,
                'message'=>'your activation link has expired',
                'code'=>1
            );
        }

        return $data;
    }

    public function forgot_password($data)
    {
        $user = $this->cek_exists_user($data['username'], $data['level']);

        if($user)
        {   
            $cek_user = User::find($user->id);
            $cek_user->remember_token = Str::random(40) . $user->email;
            $cek_user->save();

            $link_activation = env('FE_URL').'/recovery/'.$cek_user->remember_token;

            Mail::to($user->email)->send(new ForgotPassword($user->full_name, $link_activation));

            $response = array( 
                'success'=>true,
                'message'=>'Your link forgot password has been sent mail'
            );
        }else{
            $response = array( 
                'success'=>false,
                'message'=>'User Not Found',
                'errors'=>array()
            );
        }

        return $response;
    }

    public function password_recovery($data)
    {
        $kode = $data['kode'];

        $cek = User::where('remember_token', $kode)
            ->first();

        if($cek != null)
        {
            $password = $data['password'];
            $password_confirmation = $data['password_confirmation'];

            $cekPasswordLama = Hash::check($password, $cek->password);

            if($cekPasswordLama == true)
            {
                $response = array( 
                    'success'=>false,
                    'message'=>'Password cant using old password',
                    'errors'=>array()
                );
            }else{
                if($password == $password_confirmation)
                {
                    $user = User::find($cek->id);
                    $user->password = Hash::make($data['password']);
                    $user->remember_token = Str::random(40) . $cek->email;
                    $user->save();

                    $response = array( 
                        'success'=>true,
                        'message'=>'Your Password has been change'
                    );   
                }else{
                    $response = array( 
                        'success'=>false,
                        'message'=>'Password doesnt match',
                        'errors'=>array()
                    );
                }
            }

            
        }else{
            $response = array( 
                'message'=>false,
                'message'=>'User Not Found'
            );
        }

        return $response;
    }
}