<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReceptionist;
use App\Transformers\ReceptionistTransformer;

class UserReceptionistController extends Controller
{
    public function get_receptionist(){
        $model = UserReceptionist::where('tanggal',date('Y-m-d'))
            ->with(
                [
                    'user',
                    'receptionis'
                ]
            )
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
                ->where('receptionist_id',$request->input('receptionist'))
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

    public function layar($id)
    {
        $receptionist = \App\Models\Receptionist::find($id);

        $cek = UserReceptionist::where('tanggal',date('Y-m-d'))
            ->with(
                [
                    'user',
                ]
            )
            ->where('receptionist_id',$id)
            ->first();

        if($cek)
        {
            $current_antrian = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                    ->where('user_receptionist_id', $cek->id)
                    ->with(
                        [
                            'antrian',
                            'antrian.keperluan'
                        ]
                    )
                    ->first();

            $data = array(
                'success'=>true,
                'message'=>'Receptionist sudah diisi',
                'receptionist'=>$receptionist,
                'user'=>$cek,
                'current_antrian'=>$current_antrian
            );
        }else{
            $data = array(
                'success'=>false,
                'message'=>'Belum ada user untuk receptionist ini',
                'receptionist'=>$receptionist,
            );
        }

        return response()->json($data, 200);
    }

    public function all_layar(){
        $recept = \App\Models\Receptionist::all();

        $data = array();
        foreach($recept as $key=>$val)
        {
            $cek = UserReceptionist::where('tanggal',date('Y-m-d'))
                ->with(
                    [
                        'user',
                    ]
                )
                ->where('receptionist_id',$val->id)
                ->first();
            $list = array();

            if($cek)
            {
                $current_antrian = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                    ->where('user_receptionist_id', $cek->id)
                    ->with(
                        [
                            'antrian',
                            'antrian.keperluan'
                        ]
                    )
                    ->first();

                $list = array(
                    'success'=>true,
                    'message'=>'Receptionist sudah diisi',
                    'current_antrian'=>$current_antrian
                );
            }else{
                $list = array(
                    'success'=>false,
                    'message'=>'Belum ada user untuk receptionist ini',
                );
            }

            $data[]= array(
                'id'=>$val->id,
                'nama'=>$val->nama,
                'list'=>$list
            );
        }

        return $data;
    }

    public function list_antrian_by_user_receptionist($id){
        //cek antrian dulu ada atau tidak
        $tersedia = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
            ->select('antrian_id')
            ->get()
            ->pluck('antrian_id');

        $antrian_tersedia = \App\Models\Antrian::where('tanggal',date('Y-m-d'))
            ->whereNotIn('id', $tersedia)
            ->where('is_finish','N')
            ->orderBy('type','asc')
            ->orderBy('created_at','asc')
            ->get();

        if(count($antrian_tersedia) > 0)
        {
            //cek antrian untuk user receptionist tersebut ada atau tidak
            $current_antrian = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                    ->where('user_receptionist_id', $id)
                    ->with(
                        [
                            'antrian',
                            'antrian.keperluan'
                        ]
                    )
                    ->first();
            if($current_antrian)
            {
                $list_tersedia = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                    ->select('antrian_id')
                    ->get()
                    ->pluck('antrian_id');
        
                $list_other_antrian = \App\Models\Antrian::where('tanggal',date('Y-m-d'))
                    ->whereNotIn('id', $list_tersedia)
                    ->where('is_finish','N')
                    ->get();

                $data = array(
                    'success'=>true,
                    'message'=>'Antrian berhasil ditemukan',
                    'current_antrian'=>$current_antrian,
                    'other_antrian'=>$list_other_antrian
                );
            }else{
                $l_tersedia = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                    ->where('user_receptionist_id','!=', $id)
                    ->select('antrian_id')
                    ->get()
                    ->pluck('antrian_id');

                $l_antrian_tersedia = \App\Models\Antrian::where('tanggal',date('Y-m-d'))
                    ->whereNotIn('id', $l_tersedia)
                    ->where('is_finish','N')
                    ->first();

                if($l_antrian_tersedia)
                {
                    $new_antrian_user_receptionist = new \App\Models\UserReceptionistAntrian;
                    $new_antrian_user_receptionist->tanggal = date('Y-m-d');
                    $new_antrian_user_receptionist->user_receptionist_id = $id;
                    $new_antrian_user_receptionist->antrian_id = $l_antrian_tersedia->id;
                    $new_antrian_user_receptionist->save();

                    $l_current_antrian = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                        ->where('user_receptionist_id', $id)
                        ->with(
                            [
                                'antrian',
                                'antrian.keperluan'
                            ]
                        )
                        ->first();

                    $l_tersedia = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                        ->select('antrian_id')
                        ->get()
                        ->pluck('antrian_id');
            
                    $l_other_antrian = \App\Models\Antrian::where('tanggal',date('Y-m-d'))
                        ->where('id','!=',$l_current_antrian->antrian_id)
                        ->whereNotIn('id', $tersedia)
                        ->where('is_finish','N')
                        ->get();

                    $data = array(
                        'success'=>true,
                        'message'=>'Antrian berhasil dibuat',
                        'current_antrian'=>$l_current_antrian,
                        'other_antrian'=>$l_other_antrian
                    );
                }else{
                    $data = array(
                        'success'=>false,
                        'message'=>'Antrian sudah tidak ada'
                    );
                }
            }
        }else{
            $current_antrian = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                    ->where('user_receptionist_id', $id)
                    ->with(
                        [
                            'antrian',
                            'antrian.keperluan'
                        ]
                    )
                    ->first();
            if($current_antrian)
            {
                $list_tersedia = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                    ->select('antrian_id')
                    ->get()
                    ->pluck('antrian_id');
        
                $list_other_antrian = \App\Models\Antrian::where('tanggal',date('Y-m-d'))
                    ->whereNotIn('id', $list_tersedia)
                    ->where('is_finish','N')
                    ->get();

                $data = array(
                    'success'=>true,
                    'message'=>'Antrian berhasil ditemukan',
                    'current_antrian'=>$current_antrian,
                    'other_antrian'=>$list_other_antrian
                );
            }else{
                $data = array(
                    'success'=>false,
                    'message'=>'Antrian tidak tersedia'
                );
            }
        }

        return response()->json($data, 201);
    }

    public function change_antrian($id, Request $request)
    {
        $rules = [
            'antrian_id'=>'required'
        ];

        $validasi = \Validator::make($request->all(), $rules);

        if($validasi->fails())
        {
            $data = array(
                'success'=>false,
                'message'=>'Antrian harus diisi'
            );
        }else{
            $antrian_id = $request->input('antrian_id');

            $list_tersedia = \App\Models\UserReceptionistAntrian::where('tanggal',date('Y-m-d'))
                ->select('antrian_id')
                ->get()
                ->pluck('antrian_id');
    
            $list_other_antrian = \App\Models\Antrian::where('tanggal',date('Y-m-d'))
                ->whereNotIn('id', $list_tersedia)
                ->where('id','!=', $antrian_id)
                ->where('is_finish','N')
                ->orderBy('type','asc')
                ->orderBy('created_at','asc')
                ->first();

            if($list_other_antrian)
            {
                $user_antrian = \App\Models\UserReceptionistAntrian::find($id);
                $user_antrian->antrian_id = $list_other_antrian->id;
                $user_antrian->save();

                $data = array(
                    'success'=>true,
                    'message'=>'Antrian berhasil diupdate'
                );
            }else{
                $data = array(
                    'success'=>false,
                    'message'=>'Other Antrian not found'
                );
            }
        }

        return response()->json($data, 201);
    }
}
