<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use App\User;
use Illuminate\Support\Str;
use App\Area;
use App\Branch;
use App\VerifyUser;
use App\Mail\VerifyMail;
use Mail;
use App\UserLog;
use Config;

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
        $this->middleware(['auth']);
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
        $new = User::where('status', 3)->first();
        $newuser = User::where('status', 3)->update(['status' => '4']);
        $config = array(
            'driver'     => \config('mailconf.driver'),
            'host'       => \config('mailconf.host'),
            'port'       => \config('mailconf.port'),
            'from'       => \config('mailconf.from'),
            'encryption' => \config('mailconf.encryption'),
            'username'   => \config('mailconf.username'),
            'password'   => \config('mailconf.password'),
        );
        Config::set('mail', $config);
        if ($newuser) {
            Mail::send('new-user', ['email'=>$new->email],function( $message) use ($new){ 
                $message->to($new->email, $new->name.' '.$new->lastname)->subject('Account Details'); 
                $message->from('bsms.support@ideaserv.com.ph', 'BSMS support');
                $message->bcc('jolopez@ideaserv.com.ph','emorej046@gmail.com');
            });
        }
        /*if (auth()->user()->hasrole('Head')) {
            $areas = Area::where('id', auth()->user()->area->id)->get();
        }*/
        return view('pages.user', compact('users', 'areas','roles', 'title'));
    }
    public function verifyUser(Request $token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if(isset($verifyUser) ){
            $user = $verifyUser->user;
            if(!$user->verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                User::where('id', $verifyUser->user_id)->update(['email_verified_at'=> now()]);
                $status = "Your e-mail is verified. You can now login.";
            } else {
                User::where('id', $verifyUser->user_id)->update(['email_verified_at'=> now()]);
                $status = "Your e-mail is already verified. You can now login.";
            }
        } else {
            return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
        }
        return redirect('/login')->with('status', $status);
    }
    public function getUsers()
    {
        if (auth()->user()->hasrole('Manager')) {
            $users = User::query()->where('id', '!=', auth()->user()->id)->get();
        }else if(auth()->user()->hasrole('Editor')){
            $users = User::query()->select('users.*')
                ->where('id', '!=', auth()->user()->id)
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->where('role_id', '!=', '1')
                ->get();
        }else if(auth()->user()->hasrole('Warehouse Manager')){
            $users = User::query()->select('users.*')
                ->where('id', '!=', auth()->user()->id)
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->where('branch_id', auth()->user()->branch->id)
                ->where('role_id', '!=', '1')
                ->where('role_id', '!=', '4')
                ->get();
        }else{
            $users = User::query()->where('id', '!=', auth()->user()->id)
                ->where('branch_id', auth()->user()->branch->id)
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->where('role_id', '!=', '1')
                ->where('role_id', '!=', '4')
                ->get();
        }
        return DataTables::of($users)
        ->setRowData([
            'data-id' => '{{$id}}',
            'data-status' => '{{ $status }}',
        ])
        ->addColumn('fname', function (User $user){
            return $user->name.' '.$user->middlename.' '. $user->lastname;
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
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users',
            'role' => ['required', 'string'],
            'status' => ['required', 'string']
            //'password' => ['required', 'string', 'min:1', 'confirmed'],
            //password_confirmation' => 'required|same:password'
        ]);
        if ($validator->passes()) {
            $config = array(
                'driver'     => \config('mailconf.driver'),
                'host'       => \config('mailconf.host'),
                'port'       => \config('mailconf.port'),
                'from'       => \config('mailconf.from'),
                'encryption' => \config('mailconf.encryption'),
                'username'   => \config('mailconf.username'),
                'password'   => \config('mailconf.password')
            );
            Config::set('mail', $config);
            
            $user = new User;
            $user->name = ucwords(mb_strtolower($request->input('first_name')));
            $user->lastname = ucwords(mb_strtolower($request->input('last_name')));
            $user->middlename = ucwords(mb_strtolower($request->input('middle_name')));
            $user->email = $request->input('email');
            $user->area_id = $request->input('area');
            $user->branch_id = $request->input('branch');
            $user->status = 3;
            $user->password = bcrypt('123456');
            $user->assignRole($request->input('role'));
            $branch = Branch::where('id', $request->input('branch'))->first();
            $email = 'kdgonzales@ideaserv.com.ph';
            $allemails = array();
            $allemails[] = 'jerome.lopez.ge2018@gmail.com';
            /*foreach ($emails as $email) {
                $allemails[]=$email->email;
            }*/
            Mail::send('create-user', ['user'=>$user->name.' '.$user->middlename.' '.$user->lastname, 'level'=>$request->input('role'), 'branch'=>$branch->branch],function( $message) use ($allemails){ 
                $message->to('kdgonzales@ideaserv.com.ph', 'Kenneth Gonzales')->subject(auth()->user()->name.' '.auth()->user()->lastname.' has added a new user to Service center stock monitoring system.'); 
                $message->from('noreply@ideaserv.com.ph', 'Add User'); 
                $message->bcc('jolopez@ideaserv.com.ph');
            });
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = 'ADD NEW USER '.$user->name.' '.$user->lastname.' to '. $branch->branch.' office.';
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->save();
            $data = $user->save();
            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => sha1(time())
            ]);
            return response()->json($data);
        }
        return response()->json(['error'=>$validator->errors()->first()]);
    }
    public function resend(Request $request)
    {
        $checkduplicate = User::where('email', $request->email)->first();
        if ($checkduplicate) {
            if ($checkduplicate->id != auth()->user()->id) {
                $data = false;
                return response()->json(false);
            }
        }
        $verifyUser = VerifyUser::create([
            'user_id' => auth()->user()->id,
            'token' => sha1(time())
        ]);
        $user = User::where('id', auth()->user()->id)->first();
        
        User::where('id', auth()->user()->id)->update(['email' => $request->email]);
        $email = $request->email;

        $data = Mail::send('emails.verifyUser', ['user'=>$user],function( $message) use($email){ 
            $message->to($email, auth()->user()->name)->subject('Email verification'); 
            $message->from('noreply@ideaserv.com.ph', 'BSMS'); 
            $message->bcc('jerome.lopez.aks2018@gmail.com');
        });
        return response()->json(true);
        //return redirect('/login')->with('status', 'We sent you an activation code. Check your email and click on the link to verify.');
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'branch' => ['required', 'string'],
            'area' => ['required', 'string'],
            'role' => ['required', 'string'],
            'status' => ['required', 'string'],
        ]);
        if ($validator->passes()) {
            $config = array(
                'driver'     => \config('mailconf.driver'),
                'host'       => \config('mailconf.host'),
                'port'       => \config('mailconf.port'),
                'from'       => \config('mailconf.from'),
                'encryption' => \config('mailconf.encryption'),
                'username'   => \config('mailconf.username'),
                'password'   => \config('mailconf.password'),
            );
            Config::set('mail', $config);
            $olduser = User::find($id);
            $oldlevel = $olduser->roles->first()->name;
            $oldbranch = Branch::where('id', $olduser->branch_id)->first();
            $user = User::find($id);
            $user->name = ucwords(mb_strtolower($request->input('first_name')));
            $user->lastname = ucwords(mb_strtolower($request->input('last_name')));
            $user->email = $request->input('email');
            $user->area_id = $request->input('area');
            $user->middlename = ucwords(mb_strtolower($request->input('middle_name')));
            $user->branch_id = $request->input('branch');
            $user->status = $request->input('status');
            if ($olduser->email != $request->input('email')) {
                $user->email_verified_at = Null;
            }
            $data = $user->save();
            if ($request->input('status') == 1) {
                $stat = 'Active';
            }else{
                $stat = 'Inactive';
            }
            if ($olduser->status == 1) {
                $oldstat = 'Active';
            }else{
                $oldstat = 'Inactive';

            }
            $user->syncRoles($request->input('role'));
            $branch = Branch::where('id', $request->input('branch'))->first();
            Mail::send('update-user', ['oldemail'=>$olduser->email, 'newemail'=>$user->email, 'status'=>$stat,'oldstatus'=>$oldstat, 'olduser'=>$olduser->name.' '.$olduser->middlename.' '.$olduser->lastname, 'oldlevel'=>$oldlevel, 'oldbranch'=>$oldbranch->branch, 'user'=>$user->name.' '.$user->middlename.' '.$user->lastname, 'level'=>$request->input('role'), 'branch'=>$branch->branch],function( $message){ 
                $message->to('kdgonzales@ideaserv.com.ph', 'Kenneth Gonzales')->subject(auth()->user()->name.' '.auth()->user()->lastname.' has updated a user to Service center stock monitoring system.'); 
                $message->from('noreply@ideaserv.com.ph', 'Update User'); 
                $message->bcc('jolopez@ideaserv.com.ph');
            });
            return response()->json($data);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
    }
}