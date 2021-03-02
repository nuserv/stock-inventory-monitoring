<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use App\User;
use App\Area;
use App\Branch;
use App\UserLog;

use Validator;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        
        $this->middleware('auth');
    }
    public function index()
    {
        $users = User::all();
        $areas = Area::all();
        $roles = Role::all();
        $title = 'Users';
        if (!auth()->user()->hasanyrole('Manager', 'Editor','Head','Warehouse Manager')) {
            return redirect('/');
        }
        /*if (auth()->user()->hasrole('Head')) {
            $areas = Area::where('id', auth()->user()->area->id)->get();
        }*/
        return view('pages.user', compact('users', 'areas','roles', 'title'));
    }
    public function getUsers()
    {
        if (auth()->user()->hasrole('Manager')) {
            $users = User::where('id', '!=', auth()->user()->id)->get();
        }else if(auth()->user()->hasrole('Editor')){
            $users = User::select('users.*')
                ->where('id', '!=', auth()->user()->id)
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->where('role_id', '!=', '1')
                ->get();
        }else{
            $users = User::where('id', '!=', auth()->user()->id)
                ->where('branch_id', auth()->user()->branch->id)
                ->get();
        }
        return DataTables::of($users)
        ->setRowData([
            'data-id' => '{{$id}}',
            'data-status' => '{{ $status }}',
        ])
        ->addColumn('fname', function (User $user){
            return $user->name. ' ' . $user->lastname;
        })
        ->addColumn('area', function (User $user){
            return $user->area->area;
        })
        ->addColumn('branch', function (User $user){
            return $user->branch->branch;
        })
        ->addColumn('role', function (User $user){
            return $user->roles->first()->name;
        })
        ->addColumn('status', function (User $user){
            if ($user->status == 1) {
                return 'Active';
            } else {
                return 'Inactive';
            }
        })
        ->setRowClass('{{ $id % 2 == 0 ? "edittr" : "edittr"}}') 
        ->make(true);
    }
    public function getBranchName(Request $request)
    {
        $data = Branch::select('branch', 'id')->where('area_id', $request->id)->get();
        if (auth()->user()->hasrole('Head')) {
            $data = Branch::where('id', auth()->user()->branch->id)->get();
        }
        return response()->json($data);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'branch' => ['required', 'string'],
            'area' => ['required', 'string'],
            'role' => ['required', 'string'],
            'status' => ['required', 'string'],
            'password' => ['required', 'string', 'min:1', 'confirmed'],
            'password_confirmation' => 'required|same:password'
        ]);
        if ($validator->passes()) {
            $user = new User;
            $user->name = ucwords(strtolower($request->input('first_name')));
            $user->lastname = ucwords(strtolower($request->input('last_name')));
            $user->email = $request->input('email');
            $user->area_id = $request->input('area');
            $user->branch_id = $request->input('branch');
            $user->status = $request->input('status');
            $user->password = bcrypt($request->input('password'));
            $data = $user->save();
            $user->assignRole($request->input('role'));
            return response()->json($data);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'branch' => ['required', 'string'],
            'area' => ['required', 'string'],
            'role' => ['required', 'string'],
            'status' => ['required', 'string'],
        ]);
        if ($validator->passes()) {
            $user = User::find($id);
            $user->name = ucwords(strtolower($request->input('first_name')));
            $user->lastname = ucwords(strtolower($request->input('last_name')));
            $user->email = $request->input('email');
            $user->area_id = $request->input('area');
            $user->branch_id = $request->input('branch');
            $user->status = $request->input('status');
            $data = $user->save();
            $user->syncRoles($request->input('role'));
            return response()->json($data);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
    }
}