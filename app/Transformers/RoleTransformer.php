<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;

class RoleTransformer extends TransformerAbstract
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
        
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Role $model)
    {
        return [
            'id'=>$model->id,
            'name'=>$model->name,
            'count_permission'=>$model->permissions()->count(),
            'permissions'=>$model->getAllPermissions(),
            'links'=>array( 
                'detail'=> \URL::to('api/auth/role/'.$model->id)
            )
        ];
    }
}
