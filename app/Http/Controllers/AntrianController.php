<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;
use App\Transformers\AntrianTransformer;

use Barryvdh\DomPDF\Facade\Pdf;

class AntrianController extends Controller
{
    public function index(Request $request)
    {
        $model=Antrian::orderBy('tanggal','desc');

        if($request->has('per_page')){
            $halaman=$request->input('per_page');
        }else{
            $halaman=25;
        }

        $model=$model->paginate($halaman);

        $response = fractal($model, new AntrianTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function getNoAntrian(){
        $tahun = date('Y');
        $bulan = date('m');
        $hari = date('d');

        $jumlah_hari_ini = Antrian::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->whereDay('tanggal',$hari)
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

    public function antrian_pdf($id)
    {
        $antrian = Antrian::with(
            [
                'keperluan'
            ]
        )->find($id);

        // return $antrian;
        // return view('pdf.antrian')
        //     ->with('antrian',$antrian);

        $pdf = PDF::loadView('pdf.antrian',  ['antrian'=>$antrian]);
        return $pdf->setPaper('A8', 'portrait')->stream('No Antrian-'.$antrian->no_antrian.'.pdf');
    }
}
