<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReceptionist;
use App\Transformers\ReceptionistTransformer;

class UserReceptionistController extends Controller
{
    public function get_receptionist(){
        $model = UserReceptionist::where('tanggal',date('Y-m-d'))
            ->where('user_id', auth()->user()->id)
            ->first();

        if($model){
            $data = array(
                'success'=>true,
                'receptionist'=>$model
            );
        }else{
            $data = array(
                'success'=>false,
                'message'=>'Silahkan pilih receptionist terlebih dahulu'
            );
        }

        return $data;
    }
    
    public function store(Request $request)
    {
        $rules = [
            'receptionist'=>'required'
        ];

        $validasi = \Validator::make($request->all(), $rules);

        if($validasi->fails())
        {
            $data = array(
                'success'=>false,
                'message'=>"Validation errors",
                'errors'=>$validasi->errors()->all()
            );
        }else{
            //cek dulu receptionis ini sudah ada user atau belum
            $cek = UserReceptionist::where('tanggal',date('Y-m-d'))
                ->count();

            if($cek > 0)
            {
                $data = array(
                    'success'=>false,
                    'message'=>'Receptionis ini sudah ada user',
                    'errors'=>array()
                );
            }else{
                $model = new UserReceptionist;
                $model->tanggal = date('Y-m-d');
                $model->user_id = auth()->user()->id;
                $model->receptionist_id = $request->input('receptionist');
                $model->save();

                $data = array(
                    'success'=>true,
                    'message'=>'Login Receptionist berhasil',
                    'errors'=>array()
                );
            }
        }

        return response()->json($data, 201);
    }

    public function available_receptionist(){
        $model = UserReceptionist::where('tanggal',date('Y-m-d'))
            ->get()
            ->pluck('receptionist_id');

        $receptionist = \App\Models\Receptionist::whereNotIn('id', $model)
            ->get();

        $response = fractal($receptionist, new ReceptionistTransformer())
            ->toArray();

        return response()->json($response, 200);
    }
}
