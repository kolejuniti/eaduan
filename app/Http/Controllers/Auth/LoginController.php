<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        // $this->middleware('auth')->only('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();
        
        // Validate the request
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Attempt to authenticate the user
        if (auth()->attempt(['email' => $input['email'], 'password' => $input['password']])) {
            $user = auth()->user();

            // Check user type and redirect accordingly
            if ($user->type === 'technician') {
                return redirect()->route('technician.dashboard');
            } else if ($user->type === 'admin') {
                return redirect()->route('admin.dashboard');
            } else if ($user->type === 'pic') {
                return redirect()->route('pic.dashboard');
            }

            // If none of the types match, you can add a fallback
            return redirect()->route('login')->with('error', 'Invalid user type.');
        }

        // Failed login attempt
        return redirect()->route('login')->with('error', 'Invalid email or password.');
    }

    public function logout(Request $request)
    {
        // Only logout technician and admin sessions
        if (auth()->check() && in_array(auth()->user()->type, ['technician', 'admin', 'pic'])) {
            $this->guard()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Redirect to login page (or wherever you want)
        return redirect('/login');
    }
}
