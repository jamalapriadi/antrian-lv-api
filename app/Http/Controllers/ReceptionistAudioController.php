<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceptionistAudio;
use App\Transformers\ReceptionistAudioTransformer;

class ReceptionistAudioController extends Controller
{
    public function index(Request $request){
        $model=ReceptionistAudio::select('*');

        if($request->has('per_page')){
            $halaman=$request->input('per_page');
        }else{
            $halaman=25;
        }

        $model=$model->paginate($halaman);

        $response = fractal($model, new ReceptionistAudioTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function all(){
        $model = ReceptionistAudio::all();

        $response = fractal($model, new ReceptionistAudioTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function by_receptionist($id)
    {
        $model = ReceptionistAudio::where('receptionist_id',$id)
            ->orderBy('created_at','asc')
            ->get();

        $response = fractal($model, new ReceptionistAudioTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function store(Request $request){
        $rules=[
            'receptionist'=>'required',
            'antrian'=>'required',
            'audio'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validasi gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            if($request->has('kode') && $request->input('kode')!="")
            {
                $model=ReceptionistAudio::find($request->input('kode'));
            }else{
                $model=new ReceptionistAudio;
            }
            
            $model->receptionist_id = $request->input('receptionist');
            $model->no_antrian = $request->input('antrian');

            if($request->hasFile('audio')){
                if(!is_dir('uploads/audio/receptionist/')){
                    mkdir('uploads/audio/receptionist/', 0777, TRUE);
                }

                $imageData = $request->file('audio');
                $fileName = $request->audio->getClientOriginalName();

                if($imageData->move(public_path()."/uploads/audio/receptionist/",$fileName)){
                    $model->audio = $fileName;
                }
            }

            $model->save();

            $data=array(
                'success'=>true,
                'message'=>"Data berhasil disimpan",
                'error'=>''
            );
        }

        return response()->json($data, 201);
    }

    public function show($id){
        $model=ReceptionistAudio::findOrFail($id);

        $response = fractal($model, new ReceptionistAudioTransformer())
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
            $model=ReceptionistAudio::find($id);
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
        $model=ReceptionistAudio::find($id);

        $model->delete();

        $data=array(
            'success'=>true,
            'message'=>'Data berhasil dihapus',
            'error'=>''
        );

        return $data;
    }

    public function list_receptionistAudio(Request $request)
    {
        $model=ReceptionistAudio::select('*')
            ->get();

        $response = fractal($model, new ReceptionistAudioTransformer())
            ->toArray();

        return response()->json($response, 200);
    }
}
