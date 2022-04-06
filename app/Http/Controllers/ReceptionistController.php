<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receptionist;
use App\Transformers\ReceptionistTransformer;

class ReceptionistController extends Controller
{
    public function index(Request $request){
        $model=Receptionist::select('id','nama','created_at')
            ->orderBy('created_at','asc');

        if($request->has('q')){
            $model=$model->where('nama','like','%'.$request->input('q').'%');
        }

        if($request->has('per_page')){
            $halaman=$request->input('per_page');
        }else{
            $halaman=25;
        }

        $model=$model->paginate($halaman);

        $response = fractal($model, new ReceptionistTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function store(Request $request){
        $rules=[
            'nama'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validasi gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $model=Receptionist::create(
                [
                    'nama'=>$request->input('nama')
                ]
            );

            $data=array(
                'success'=>true,
                'message'=>"Data berhasil disimpan",
                'error'=>''
            );
        }

        return response()->json($data, 201);
    }

    public function show($id){
        $model=Receptionist::findOrFail($id);

        $response = fractal($model, new ReceptionistTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function update(Request $request,$id){
        $rules=[
            'nama'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validasi gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $model=Receptionist::find($id);
            $model->nama=$request->input('nama');
            $model->save();

            $data=array(
                'success'=>true,
                'message'=>"Data berhasil diupdate",
                'error'=>''
            );
        }

        return response()->json($data, 201);
    }

    public function destroy(Request $request,$id){
        $model=Receptionist::find($id);

        $model->delete();

        $data=array(
            'success'=>true,
            'message'=>'Data berhasil dihapus',
            'error'=>''
        );

        return $data;
    }

    public function list_Receptionist(Request $request)
    {
        $model=Receptionist::select('id','nama')->get();

        $response = fractal($model, new ReceptionistTransformer())
            ->toArray();

        return response()->json($response, 200);
    }
}
