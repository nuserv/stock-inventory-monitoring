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
use App\VerifyUser;
use App\Mail\VerifyMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;
use Auth;
use Config;
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

    public function verifyUser(Request $request, $id)
    {
        $verifyUser = VerifyUser::where('token', $id)->first();
        if(isset($verifyUser) ){
            $user = $verifyUser->user;
            if(!$user->verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                User::where('id', $verifyUser->user_id)->update(['email_verified_at'=> now()]);
                $verifyUser->delete();
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

    public function authenticated(Request $request, $user) {
        $log = new UserLog;
        $log->branch_id = auth()->user()->branch->id;
        $log->branch = auth()->user()->branch->branch;
        $log->activity = "Sign-in.";
        $log->user_id = auth()->user()->id;
        $log->fullname = auth()->user()->name.' '.auth()->user()->middlename.' '.auth()->user()->lastname;
        $log->save();
        $unverify = User::where('status', 4)->update(['status' => '1']);
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
        if ($unverify) {
            \Mail::to($user->email)->send(new VerifyMail($user));
        }
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
