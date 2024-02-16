<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    function __construct()
    {

        $this->middleware('permission:User_List', ['only' => ['index','show']]);//for Admin
        $this->middleware('permission:Add_User', ['only' => ['create','store']]);// for Admin
        $this->middleware('permission:Edit_Profile', ['only' => ['edit','update']]);// for user
        $this->middleware('permission:control_User', ['only' => ['update_status']]);// for Admin
        $this->middleware('permission:Delete_User', ['only' => ['delete']]);//for Admin


    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::all();
        return response()->json([
            'status'=>true,
            'message'=>true,
            '$users'=>$users
        ],201);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'categories'=>$user
        ],201);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole([$request->input('roles')]);
        return redirect()->route('users.index')
            ->with('success','User created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json([
            'status'=>true,
            'message'=>true,
            'categories'=>$user
        ],201);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'categories'=>$user
        ],201);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $rules = array(
            'name' => 'required|string',
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $model_has_roles=DB::table('model_has_roles')->where('model_id',$id)->first();


        $user = User::find($id);
        if(!$model_has_roles){
            $role_name=DB::table('roles')->where('name',$user->roles_name)->first();
            $model_has_roles->create([
                'role_id'=>$role_name->id,
                'model_type'=>'App\Models\User',
                'model_id'=>$id

            ]);
        }
        $user->update([
            'name' => $input['name']
        ]);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->input('roles'));
        return response()->json([
            'status'=>true,
            'message'=>true,
            'categories'=>$user
        ],201);
    }

    public function update_status(Request $request, $id)
    {
        $input = $request->all();


        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->input('roles'));
        return response()->json([
            'status'=>true,
            'message'=>true,
            'categories'=>$user
        ],201);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $user=User::find($id)->delete();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'categories'=>$user
        ],201);
    }



}
