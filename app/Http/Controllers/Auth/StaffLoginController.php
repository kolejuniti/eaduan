<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{
    public function showloginform()
    {
        // Check if staff is already authenticated
        if (Auth::guard('staff')->check()) {
            // Redirect to the student dashboard if already logged in
            return redirect()->route('staff.dashboard');
        }

        // Show the login form if not authenticated
        return view('auth.staff.login');
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to log the staff in
        if (Auth::guard('staff')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->remember
        )) {
            // If successful, redirect to intended location
            return redirect()->intended(route('staff.dashboard'));
        }

        // If unsuccessful, redirect back with input
        return redirect()->back()->with('error', 'Salah emel atau kata laluan tidak sah.');
    }

    public function logout()
    {
         // Logout only the staff
        Auth::guard('staff')->logout();
        
        // Invalidate the stustaffdent session without flushing the entire session
        session()->forget('staff');  // Assuming you're using 'staff' session key

        // Redirect to the staff login page
        return redirect()->route('staff.login');
    }

}
