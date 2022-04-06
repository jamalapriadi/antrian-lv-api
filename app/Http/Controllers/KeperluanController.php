<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keperluan;
use App\Transformers\KeperluanTransformer;

class KeperluanController extends Controller
{
    public function index(Request $request){
        $model=Keperluan::select('id','nama','no_urut','created_at')
            ->orderBy('no_urut','asc');

        if($request->has('q')){
            $model=$model->where('nama','like','%'.$request->input('q').'%');
        }

        if($request->has('per_page')){
            $halaman=$request->input('per_page');
        }else{
            $halaman=25;
        }

        $model=$model->paginate($halaman);

        $response = fractal($model, new KeperluanTransformer())
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
            $model=Keperluan::create(
                [
                    'nama'=>$request->input('nama'),
                    'no_urut'=>$request->input('urut')
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
        $model=Keperluan::findOrFail($id);

        $response = fractal($model, new KeperluanTransformer())
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
            $model=Keperluan::find($id);
            $model->nama=$request->input('nama');
            $model->no_urut = $request->input('urut');
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
        $model=Keperluan::find($id);

        $model->delete();

        $data=array(
            'success'=>true,
            'message'=>'Data berhasil dihapus',
            'error'=>''
        );

        return $data;
    }

    public function list_keperluan(Request $request)
    {
        $model=Keperluan::select('id','nama')
            ->orderBy('no_urut','asc')
            ->get();

        $response = fractal($model, new KeperluanTransformer())
            ->toArray();

        return response()->json($response, 200);
    }
}
