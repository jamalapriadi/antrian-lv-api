<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;

class UserTransformer extends TransformerAbstract
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
    public function transform(User $model)
    {
        return [
            'id'=>$model->id,
            'name'=> $model->name,
            'email'=> $model->email,
            'slug'=> $model->slug,
            'profile_picture_url'=>asset('img/user/'.$model->profile_picture),
            'role'=>$model->roles,
            'permissions'=>$model->getAllPermissions(),
            'active'=>$model->active,
            'links'=>array( 
                'detail'=> \URL::to('api/auth/user/'.$model->id)
            )
        ];
    }
}
