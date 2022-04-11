<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Antrian;

class AntrianTransformer extends TransformerAbstract
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
    public function transform(Antrian $model)
    {
        $kategori = "";
        if($model->type == 1)
        {
            $kategori = "PRIORITAS";
        }else{
            $kategori = "UMUM";
        }

        return [
            'id'=>$model->id,
            'tanggal'=>$model->tanggal,
            'kategori'=>$kategori,
            'no_antrian'=>$model->no_antrian,
            'keperluan'=>$model->keperluan->nama
        ];
    }
}
