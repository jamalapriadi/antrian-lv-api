<?php

namespace App\Services;
use App\Repositories\Eloquent\UserRepository;
use App\Models\User;

class UserService
{
    private $model;

    public function __construct(UserRepository $model)
    {
        $this->model = $model;
    }

    public function all(){
        $model = $this->model->getAll();

        return $model;
    }

    public function show($id)
    {
        return $this->model->getById($id);
    }

    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

    public function per_page($data){
        return $this->model->perPage($data);
    }

    public function activate_user($id, $data)
    {
        $attributes = array(
            'active'=>'N'
        );

        $user = User::find($id);
        $user->active = $data['status'];
        $user->save();

        return $user;
        
    }
}