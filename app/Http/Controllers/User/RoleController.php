<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Transformers\RoleTransformer;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request){
        $role=Role::select('id','name');

        if($request->has('q')){
            $role=$role->where('name','like','%'.$request->input('q').'%');
        }

        if($request->has('per_page')){
            $halaman=$request->input('per_page');
        }else{
            $halaman=25;
        }

        $role=$role->paginate($halaman);

        $response = fractal($role, new RoleTransformer())
            ->includeJumlahpermission()
            ->toArray();

        return response()->json($response, 200);
    }

    public function store(Request $request){
        $rules=[
            'nama'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $role = Role::create(['name' => $request->input('nama')]);

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil disimpan',
                'error'=>''
            );
        }

        return response()->json($data, 201);
    }

    public function show($id){
        $role=Role::with('permissions')->findOrFail($id);

        $response = fractal($role, new RoleTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function update(Request $request,$id){
        $rules=[
            'nama'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $role=Role::find($id)            
                ->update(
                    [
                        'name'=>$request->input('nama')
                    ]
                );

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil disimpan',
                'error'=>''
            );
        }

        return response()->json($data, 201);
    }

    public function destroy($id){
        $role=Role::find($id);

        $hapus=$role->delete();

        if($hapus){
            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil dihapus',
                'error'=>''
            );
        }else{
            $data=array(
                'success'=>false,
                'pesan'=>'Data gagal dihapus',
                'error'=>''
            );
        }

        return response()->json($data, 200);
    }

    public function list_role_with_permission($id){
        $user=\App\Models\User::with('permissions','roles')->find($id);
        $role=Role::with('permissions')->get();

        return array(
            'user'=>$user,
            'role'=>$role
        );
    }

    public function list_role(Request $request)
    {
        $role=Role::with('permissions')->get();

        $response = fractal($role, new RoleTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function save_role_permission($id, Request $request)
    {
        $role = Role::find($id);

        \DB::Table('role_has_permissions')
            ->where('role_id', $id)
            ->delete();
            
        foreach($request->all() as $key=>$val){
            $permission = Permission::find($val['id']);

            $role->givePermissionTo($permission);
        }

        $data=array(
            'success'=>true,
            'pesan'=>'Permission berhasil diset',
            'error'=>''
        );

        return response()->json($data, 201);
    }
}