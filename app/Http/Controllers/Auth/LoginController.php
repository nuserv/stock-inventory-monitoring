<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Spatie\Permission\Models\Role;
use App\Branch;
use App\Area;
use Jenssegers\Agent\Agent;
use App\User;
use App\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Session;
use Redirect;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticated(Request $request, $user) {
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
                $log->branch = auth()->user()->branch->branch;
        $log->activity = "Sign-in.";
        $log->user_id = auth()->user()->id;
                $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
    }
    public function logout() {
        if (!Auth::guest()) {
            $log = new UserLog;
            $log->branch_id = auth()->user()->branch->id;
            $log->branch = auth()->user()->branch->branch;
            $log->activity = "Sign-out.";
            $log->user_id = auth()->user()->id;
            $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
            $log->save();
            Auth::logout(); // logout user
            Session::flush();
            Redirect::back();
        }
        return Redirect::to('login'); //redirect back to login
    }
}
