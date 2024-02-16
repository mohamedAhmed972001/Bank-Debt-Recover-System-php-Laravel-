<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {

        $this->middleware('permission:Roles', ['only' => ['index','show','create','store','edit','update','delete']]);//for Admin

    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return response()->json([
            'message' => true,
            'user' => $roles
        ], 201);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();
        return response()->json([
            'message' => true,
            'Permissions' => $permission
        ], 201);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $role = Role::create(['name' => $input['name']]);
        $role->syncPermissions($input['permission']);
        return response()->json([
            'message' => true,
            'Role' => $role
        ], 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        return response()->json([
            'message' => true,
            'Role' => $role
        ], 201);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return response()->json([
            'message' => true,
            'Role' => $role
        ], 201);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $rules = array(
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
        return response()->json([
            'message' => true,
            'Role' => $role
        ], 201);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $role=DB::table("roles")->where('id',$id)->delete();
        return response()->json([
            'message' => true,
            'Role' => $role
        ], 201);
    }
}
