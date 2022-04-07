<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;

class AntrianController extends Controller
{
    private function getNoAntrian(){
        $jumlah_hari_ini = Antrian::where('tanggal',date('Y-m-d'))
            ->count();

        $ditambah_satu = $jumlah_hari_ini + 1;

        $hasil = "";
        if($jumlah_hari_ini >= 100)
        {
            $hasil = "A".$ditambah_satu;
        }else if($jumlah_hari_ini >= 10 && $jumlah_hari_ini < 100)
        {
            $hasil = "A-0".$ditambah_satu;
        }else{
            $hasil = "A-00".$ditambah_satu;
        }

        return $hasil;
    }

    public function simpan_antrian(Request $request){
        $rules = [
            'keperluan'=>'required',
            'type'=>'required'
        ];

        $validasi = \Validator::make($request->all(), $rules);

        if($validasi->fails())
        {
            $data = array(
                'success'=>false,
                'message'=>'Validasi Errors',
            );
        }else{
            $model = new Antrian;
            $model->type = $request->input('type');
            $model->tanggal = date('Y-m-d');
            $model->no_antrian = $this->getNoAntrian();
            $model->keperluan_id = $request->input('keperluan');
            $model->save();

            $list_antrian = Antrian::with(
                [
                    'keperluan'
                ]
            )->find($model->id);

            $data = array(
                'success'=>true,
                'message'=>'Antrian Berhasil dibuat',
                'data'=>$list_antrian
            );
        }

        return response()->json($data, 201);
    }
}
