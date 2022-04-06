<?php

namespace App\Http\Controllers;

use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use \App\Models\User;

class AuthController extends Controller
{
    public function me(Request $request)
    {
        $response = fractal($request->user(), new UserTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();

        return response()->json(['token' => $user->createToken($user->name)->plainTextToken]);
    }

    public function update_password(Request $request)
    {
        $this->validate($request, [
            'old_password'=>'required',
            'password'=>'required|confirmed',
            'password_confirmation'=>'required'
        ]);

        $old_password = $request->input('old_password');
        $password = $request->input('password');
        $password_confirmation = $request->input('password_confirmation');

        $cekPassword = Hash::check($old_password, auth()->user()->password);

        if($cekPassword == true)
        {
            $cekPasswordLama = Hash::check($password, auth()->user()->password);

            if($cekPasswordLama == true)
            {
                $response = array( 
                    'success'=>false,
                    'message'=>'Cant use old password',
                    'errors'=>array()
                );
    
                return response()->json($response, 403);
            }else{
                if($password == $password_confirmation)
                {
                    $user = \App\Models\User::find(auth()->user()->id);
                    $user->password = Hash::make($password);
                    $user->save();

                    $response = array( 
                        'success'=>true,
                        'message'=>'Your Password has been change'
                    );   

                    return response()->json($response, 201);
                }else{
                    $response = array( 
                        'success'=>false,
                        'message'=>'Password doesnt match',
                        'errors'=>array()
                    );

                    return response()->json($response, 403);
                }
            }

        }else{
            $response = array( 
                'success'=>false,
                'message'=>'Current Password didnt match',
                'errors'=>array()
            );

            return response()->json($response, 403);
        }
    }
    
    public function logout(Request $request) {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $respon = [
            'status' => 'success',
            'msg' => 'Logout successfully',
            'errors' => null,
            'content' => null,
        ];
        return response()->json($respon, 200);
    }

    public function logoutall(Request $request) {
        $user = $request->user();
        $user->tokens()->delete();
        $respon = [
            'status' => 'success',
            'msg' => 'Logout successfully',
            'errors' => null,
            'content' => null,
        ];
        return response()->json($respon, 200);
    }

    public function change_password(Request $request)
    {
        $rules = [
            'current' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ];

        $pesan = [
            'current.required' => 'Current password harus diisi',
            'password.required' => 'Password harus diisi',
            'password_confirmation.required' => 'Confirmasi password harus diisi'
        ];

        $validasi = \Validator::make($request->all(), $rules, $pesan);

        if ($validasi->fails()) {
            $data = array(
                'success' => false,
                'message' => 'Validasi gagal',
                'error' => $validasi->errors()->all()
            );
        } else {
            if (\Hash::check($request->input('current'), \Auth::user()->password)) {
                $user = \App\Models\User::find($request->user()->id);
                $user->password = \Bcrypt($request->input('password'));
                $user->save();

                $data = array(
                    'success' => true,
                    'message' => 'Password has been change',
                    'error' => ''
                );
            } else {
                $data = array(
                    'success' => false,
                    'message' => 'Current password is wrong',
                    'error' => ''
                );
            }
        }

        return response()->json($data, 200);
    }

    public function update_foto(Request $request)
    {
        $rules = ['file' => 'required'];

        $validasi = \Validator::make($request->all(), $rules);

        if ($validasi->fails()) {
            $data = array(
                'success' => false,
                'pesan' => "Validasi Error",
                'errors' => $validasi->errors()->all()
            );
        } else {
            $user = User::find($request->user()->id);

            if ($request->hasFile('file')) {
                if (!is_dir('img/user/')) {
                    mkdir('img/user/', 0777, TRUE);
                }

                $file = $request->file('file');
                $filename = str_random(5) . '-' . $file->getClientOriginalName();
                $filename = $file->getClientOriginalName();
                $destinationPath = "img/user/";

                if ($file->move($destinationPath, $filename)) {
                    $user->avatar_url = $filename;
                }
            }

            $user->save();

            $data = array(
                'success' => true,
                'pesan' => 'Data berhasil diubah',
                'error' => ''
            );
        }

        return $data;
    }

    public function update_info(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $validasi = \Validator::make($request->all(), $rules);

        if ($validasi->fails()) {
            $data = array(
                'success' => false,
                'message' => "Validasi Error",
                'errors' => $validasi->errors()->all()
            );
        } else {
            $user = User::find($request->user()->id);
            $user->name = $request->input('name');

            if ($request->has('file') && $request->input('file') != "") {
                if (!is_dir('img/user/')) {
                    mkdir('img/user/', 0777, TRUE);
                }

                $imageData = $request->input('file');

                $folderPath = public_path('img/user/');

                $image_parts = explode(";base64,", $imageData);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);

                $filename = uniqid() . '.' . $image_type;
                $file = $folderPath . $filename;

                file_put_contents($file, $image_base64);

                $user->avatar_url = $filename;
            }

            $user->save();

            $data = array(
                'success' => true,
                'message' => 'Data berhasil diubah',
                'error' => ''
            );
        }

        return $data;
    }
}
