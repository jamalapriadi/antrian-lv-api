<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelayanan;
use App\Transformers\PelayananTransformer;

class PelayananController extends Controller
{
    public function index(Request $request){
        $model=Pelayanan::select('*');

        if($request->has('q')){
            $model=$model->where('nama','like','%'.$request->input('q').'%');
        }

        if($request->has('per_page')){
            $halaman=$request->input('per_page');
        }else{
            $halaman=25;
        }

        $model=$model->paginate($halaman);

        $response = fractal($model, new PelayananTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function store(Request $request){
        $rules=[
            'user_receptionist_id'=>'required',
            'nama'=>'required',
            'antrian_id'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validasi gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $model=Pelayanan::create(
                [
                    'tanggal'=>date('Y-m-d'),
                    'user_receptionist_id'=>$request->input('user_receptionist_id'),
                    'antrian_id'=>$request->input('antrian_id'),
                    'nama'=>$request->input('nama'),
                    'phone'=>$request->input('phone'),
                    'alamat'=>$request->input('alamat'),
                    'catatan'=>$request->input('catatan'),
                    'user_id'=>auth()->user()->id,
                    'is_finish'=>'Y'
                ]
            );

            //update antrian
            $antrian = \App\Models\Antrian::find($request->input('antrian_id'));
            $antrian->is_finish= 'Y';
            $antrian->save();

            //delete user receptionist
            $user_receptionist = \App\Models\UserReceptionistAntrian::find($request->input('user_receptionist_id'));
            $user_receptionist->delete();

            $data=array(
                'success'=>true,
                'message'=>"Data berhasil disimpan",
                'error'=>''
            );
        }

        return response()->json($data, 201);
    }

    public function show($id){
        $model=Pelayanan::findOrFail($id);

        $response = fractal($model, new PelayananTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function destroy(Request $request,$id){
        $model=Pelayanan::find($id);

        $model->delete();

        $data=array(
            'success'=>true,
            'message'=>'Data berhasil dihapus',
            'error'=>''
        );

        return $data;
    }
}
