<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLoginController extends Controller
{
    public function showloginform()
    {
        // Check if student is already authenticated
        if (Auth::guard('student')->check()) {
            // Redirect to the student dashboard if already logged in
            return redirect()->route('student.dashboard');
        }

        // Show the login form if not authenticated
        return view('auth.student.login');
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'no_matric' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to log the student in
        if (Auth::guard('student')->attempt(
            ['no_matric' => $request->no_matric, 'password' => $request->password],
            $request->remember
        )) {
            // If successful, redirect to intended location
            return redirect()->intended(route('student.dashboard'));
        }

        // If unsuccessful, redirect back with input
        return redirect()->back()->with('error', [
            'Salah no. matriks',
            'Kata laluan tidak sah.',
            'Status pelajar tidak aktif.'
        ]);
    }

    public function logout()
    {
         // Logout only the student
        Auth::guard('student')->logout();
        
        // Invalidate the student session without flushing the entire session
        session()->forget('student');  // Assuming you're using 'student' session key

        // Redirect to the student login page
        return redirect()->route('student.login');
    }

}
