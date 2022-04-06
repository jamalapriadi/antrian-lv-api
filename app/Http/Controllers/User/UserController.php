<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\User;
use Carbon\Carbon;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use App\Transformers\UserTransformer;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request){
        $user=User::with('roles');

        if($request->has('q')){
            $user=$user->where('name','like','%'.request('q').'%');
        }

        if($request->has('per_page')){
            $halaman=$request->input('per_page');
        }else{
            $halaman=25;
        }

        $user = $user->paginate($halaman);

        $response = fractal($user, new UserTransformer())
            ->toArray();

        return response()->json($response, 200);

    }

    public function all(){
        $user = User::all();
        
        $response = fractal($user, new UserTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function store(Request $request){
        $rules=[
            'nama'=>'required',
            'password'=>'required',
            'password_confirm'=>'required|same:password',
            'email'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $user=new User;
            $user->name=$request->input('nama');
            $user->email=$request->input('email');
            $user->password=bcrypt($request->input('password'));

            if($request->has('image') && $request->input('image')!=""){
                if(!is_dir('img/user/')){
                    mkdir('img/user/', 0777, TRUE);
                }

                $imageData = $request->input('image');
                $filename = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
                Image::make($request->input('image'))->save(public_path('img/user/').$filename);
                $user->profile_picture=$filename;
            }
            
            $simpan=$user->save();

            if($simpan){
                if($request->has('role')){
                    $role=$request->input('role');

                    $user->roles()->sync([$user->id => ['role_id'=>$role]]);
                }

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
        }

        return response()->json($data, 201);
    }

    public function show($id){
        $user=User::findOrFail($id);
        
        $response = fractal($user, new UserTransformer())
            ->toArray();

        return response()->json($response, 200);
    }

    public function update(Request $request,$id){
        $rules=[
            'nama'=>'required',
            'email'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi Error',
                'error'=>$validasi->errors()->all()
            );
        }else{
            $user=User::find($id);
            $user->name=$request->input('nama');
            $user->email=$request->input('email');
            $user->password=bcrypt($request->input('password'));
            
            $simpan=$user->save();

            if($simpan){
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
        }

        return response()->json($data, 201);
    }

    public function destroy($id){
        $user=User::find($id);

        $hapus=$user->delete();

        if($hapus){
            $data=array(
                'success'=>true,
                'pesan'=>"Data berhasil dihapus",
                'error'=>''
            );
        }else{
            $data=array(
                'success'=>false,
                'pesan'=>'Data gagal dihapus',
                'error'=>''
            );
        }

        return $data;
    }

    public function list_role(Request $request,$id){
        $user=User::with('roles')->find($id);

        return $user;
    }

    public function save_role_user(Request $request){
        $rules=['permission'=>'required','user'=>'required'];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi gagal',
                'error'=>''
            );
        }else{
            $user=User::find($request->input('user'));

            $permission=$request->input('permission');
            foreach($permission as $key=>$val){
                $user->givePermissionTo($val);
            }

            $data=array(
                'success'=>true,
                'pesan'=>'Permission berhasil disimpan',
                'error'=>''
            );
        }

        return $data;
    }

    public function hapus_role_user(Request $request){
        $rules=['permission'=>'required','user'=>'required'];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>'Validasi error',
                'error'=>''
            );
        }else{
            $user=User::find($request->input('user'));

            $permission=$request->input('permission');

            $user->revokePermissionTo($permission);

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil dihapus',
                'error'=>''
            );
        }
        
        return $data;
    }

    public function change_password(Request $request){
        if($request->ajax()){
            $rules=[
                'current'=>'required',
                'password'=>'required',
                'password_confirmation'=>'required|same:password'
            ];

            $pesan=[
                'current.required'=>'Current password harus diisi',
                'password.required'=>'Password harus diisi',
                'password_confirmation.required'=>'Confirmasi password harus diisi'
            ];

            $validasi=\Validator::make($request->all(),$rules,$pesan);

            if($validasi->fails()){
                $data=array(
                    'success'=>false,
                    'pesan'=>'Validasi gagal',
                    'error'=>$validasi->errors()->all()
                );
            }else{
                if(\Hash::check($request->input('current'), \Auth::user()->password)){
                    $user=\App\Models\User::find(auth()->user()->id);
                    $user->password=\Bcrypt($request->input('password'));
                    $user->save();

                    $data=array(
                        'success'=>true,
                        'pesan'=>'Password has been change',
                        'error'=>''
                    );

                    \Auth::logout();
                }else{
                    $data=array(
                        'success'=>false,
                        'pesan'=>'Current password wrong',
                        'error'=>''
                    );
                }
            }

            return $data;
        }
    }

    public function update_foto(Request $request)
    {
        $rules=['file'=>'required'];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'pesan'=>"Validasi Error",
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $user=User::find(auth()->user()->id);

            if($request->hasFile('file')){
                if(!is_dir('img/user/')){
                    mkdir('img/user/', 0777, TRUE);
                }

                $file=$request->file('file');
                $filename=str_random(5).'-'.$file->getClientOriginalName();
                $filename=$file->getClientOriginalName();
                $destinationPath="img/user/";

                if($file->move($destinationPath,$filename)){
                    $user->images=$filename;
                }
            }

            $user->save();

            $data=array(
                'success'=>true,
                'pesan'=>'Data berhasil diubah',
                'error'=>''
            );
        }

        return $data;
    }

    public function update_info(Request $request)
    {
        $rules=[
            'name'=>'required',
            'desc'=>'required'
        ];

        $validasi=\Validator::make($request->all(),$rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>"Validasi Error",
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $user=User::find(auth()->user()->id);
            $user->name= $request->input('name');
            $user->description=$request->input('desc');

            if($request->has('file') && $request->input('file')!=""){
                if(!is_dir('img/user/')){
                    mkdir('img/user/', 0777, TRUE);
                }

                $imageData = $request->input('file');
                $filename = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
                Image::make($request->input('file'))->save(public_path('img/user/').$filename);
                $user->avatar_url=$filename;
            }

            $user->save();

            $data=array(
                'success'=>true,
                'message'=>'Data berhasil diubah',
                'error'=>''
            );
        }

        return $data;
    }

    public function follower(User $user){
        $follower=auth()->user();

        if($follower->id==$user->id){
            return back()->withError("You can't follow yourselft");
        }

        if(!$follower->isFollowing($user->id)){
            $follower->follow($user->id);

            //sending notification
            $user->notify(new UserFollowed($follower));

            return back()->withSuccess("You are now friends with {$user->name}");
        }

        return back()->withError("You are already following {$user->name}");
    }

    public function unfollow(User $user){
        $follower=auth()->user();
        if($follower->isFollowing($user->id)){
            $follower->unfollow($user->id);
            return back()->withSuccess("You are not longer friends with {$user->name}");
        }

        return back()->withError("You are not following {$user->name}");
    }

    public function notifications(){
        return auth()->user()->unreadNotification()->limit(5)->aget()->toArray();
    }

    public function status_user(Request $request, $id)
    {
        $rules=[
            'status'=>'required'
        ];

        $validasi=\Validator::make($request->all(), $rules);

        if($validasi->fails()){
            $data=array(
                'success'=>false,
                'message'=>'Validasi error',
                'errors'=>$validasi->errors()->all()
            );
        }else{
            $user=User::find($id);
            $user->active=$request->input('status');
            $user->save();

            $data=array(
                'success'=>true,
                'message'=>'Data has been saved',
                'errors'=>array()
            );
        }

        return response()->json($data, 201);
    }

    public function update_role_user(Request $request,$id)
    {
        $user=User::find($id);

        \DB::table('model_has_roles')
            ->where('model_id',$id)
            ->delete();

        $list = array();
        foreach($request->all() as $key=>$val)
        {
            $role = Role::find($val['id']);

            $list[]= $role->name;
        }

        $user->syncRoles($list);

        $data=array(
            'success'=>true,
            'message'=>'Role has been updated',
            'erros'=>array()
        );

        return response()->json($data, 201);
    }

    public function login_info(){
        $id=auth()->user()->id;

        $user=\App\Models\User::with('roles')->find($id);

        return $user;
    }

    public function reset_password_user(Request $request, $id)
    {
        $password_default='welcome'.date('Y');

        $user=\App\Models\User::findOrFail($id);
        $user->password=bcrypt($password_default);
        $user->save();


        return array(
            'success'=>true,
            'message'=>'Password sudah di reset, passwordnya adalah '.$password_default,
            'error'=>array()
        );
    }

    public function socialite(Request $request)
    {
        $social = \App\Models\Social::where('user_id',auth()->user()->id)
            ->get();

        return $social;
    }
}