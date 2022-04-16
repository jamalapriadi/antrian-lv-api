<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ReceptionistAudio;

class ReceptionistAudioTransformer extends TransformerAbstract
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
    public function transform(ReceptionistAudio $model)
    {
        return [
            'id'=>$model->id,
            'receptionist_id'=>$model->receptionist_id,
            'no_antrian'=>$model->no_antrian,
            'audio'=>$model->audio,
            'audio_url'=>asset('uploads/audio/receptionist/'.$model->audio),
            'links'=>array( 
                'detail'=> \URL::to('api/auth/receptionist-audio/'.$model->id)
            )
        ];
    }
}
