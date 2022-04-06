<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Transformers\PermissionTransformer;

class PermissionController extends Controller
{
    public function index(Request $request){
        $permission=Permission::select('id','name');

        if($request->has('q')){
            $permission=$permission->where('name','like','%'.$request->input('q').'%');
        }

        if($request->has('per_page')){
            $halaman=$request->input('per_page');
        }else{
            $halaman=25;
        }

        $permission=$permission->paginate($halaman);

        $response = fractal($permission, new PermissionTransformer())
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
                'message'=>'Validasi gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $permission=Permission::create(['name'=>$request->input('nama')]);

            $data=array(
                'success'=>true,
                'message'=>"Data berhasil disimpan",
                'error'=>''
            );
        }

        return response()->json($data, 201);
    }

    public function show($id){
        $permission=Permission::findOrFail($id);

        $response = fractal($permission, new PermissionTransformer())
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
                'message'=>'Validasi gagal',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $permission=Permission::find($id);
            $permission->name=$request->input('nama');
            $permission->save();

            $data=array(
                'success'=>true,
                'message'=>"Data berhasil diupdate",
                'error'=>''
            );
        }

        return response()->json($data, 201);
    }

    public function destroy(Request $request,$id){
        $permission=Permission::find($id);

        $permission->delete();

        $data=array(
            'success'=>true,
            'message'=>'Data berhasil dihapus',
            'error'=>''
        );

        return $data;
    }

    public function list_permission(Request $request)
    {
        $permission=Permission::select('id','name')->get();

        $response = fractal($permission, new PermissionTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function save_permission_role(Request $request,$id)
    {
        $rules=['roles'=>'required'];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validation error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $perm=$request->input('roles');
            $role=Role::find($id);

            \DB::table('role_has_permissions')
                ->where('role_id',$id)
                ->delete();

            foreach($perm as $row){
                if($row!=null){
                    $role->givePermissionTo($row['name']);
                }
            }

            $data=array(
                'success'=>true,
                'message'=>'Data has been save',
                'errors'=>array()
            );
        }

        return $data;
        
    }
}