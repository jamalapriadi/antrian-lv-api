<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Receptionist;

class ReceptionistTransformer extends TransformerAbstract
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
    public function transform(Receptionist $model)
    {
        return [
            'id'=>$model->id,
            'nama'=>$model->nama,
            'created_at'=>$model->created_at,
            'links'=>array( 
                'detail'=> \URL::to('api/auth/receptionist/'.$model->id)
            )
        ];
    }
}
