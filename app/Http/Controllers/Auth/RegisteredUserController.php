<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employer;
use App\Models\WorkerProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:worker,employer'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Create role-specific records
        if ($user->role === 'employer') {
            Employer::create([
                'user_id' => $user->id,
                'company_name' => $user->name, // Default to user name, can be updated later
            ]);
        } elseif ($user->role === 'worker') {
            WorkerProfile::create([
                'user_id' => $user->id,
                'first_name' => '',
                'last_name' => '',
                'nationality_country_code' => '',
                'birth_year' => 1940,
                'skills' => [],
            ]);
        }

        event(new Registered($user));

        // Auto-login workers, but not employers (they need email verification + approval)
        if ($user->role === 'worker') {
            Auth::login($user);
            return redirect('/jobs');
        } else {
            // Employers must verify email and wait for approval
            return redirect('/employer/login')->with('message', 
                'Account created successfully! Please verify your email. After verification, your account must be approved before you can post jobs.'
            );
        }
    }
}
