<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Pelayanan;

class PelayananTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Pelayanan $model)
    {
        $kategori = "";
        if($model->antrian)
        {
            if($model->antrian->type == 1){
                $kategori = "Prioritas";
            }else if($model->antrian->type == 2){
                $kategori = "Umum";
            }
        }
        return [
            'id'=>$model->id,
            'tanggal'=>$model->tanggal,
            'user_receptionist_id'=>$model->user_receptionist_id,
            'antrian_id'=>$model->antrian_id,
            'nama'=>$model->nama,
            'phone'=>$model->phone,
            'alamat'=>$model->alamat,
            'catatan'=>$model->catatan,
            'user_id'=>$model->user_id,
            'created_at'=>$model->created_at,
            'antrian'=>$model->antrian,
            'keperluan'=>$model->antrian->keperluan,
            'kategori'=>$kategori,
            'links'=>array( 
                'detail'=> \URL::to('api/auth/pelayanan/'.$model->id)
            )
        ];
    }
}
