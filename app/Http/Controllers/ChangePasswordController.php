<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;

class ChangePasswordController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index()
    {
        $title = 'Change password';
        return view('changePassword', compact('title'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
        return redirect()->back()->with('success', 'Password change successfully.');
    }
    
    public function confirm(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($user->hasrole('Head') && auth()->user()->branch->id == $user->branch_id) {
                if (!Hash::check($request->password, $user->password)) {
                    return response()->json('Login Fail, pls check password!');
                }else{
                    return response()->json('success');
                }
            }else{
                return response()->json('Not Allowed!');
            }
        }else{
            return response()->json('Login Fail, Please check email!');
        }
    }
}
