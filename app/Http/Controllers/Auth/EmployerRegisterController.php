<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employer;
use App\Notifications\AdminNewEmployerPending;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class EmployerRegisterController extends Controller
{
    /**
     * Display the employer registration view.
     */
    public function create(): View
    {
        return view('employer.register');
    }

    /**
     * Handle an incoming employer registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:120'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        // Create user with employer role
        $user = User::create([
            'name' => $request->contact_name ?? $request->company_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_EMPLOYER,
        ]);

        // Create employer record
        Employer::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'city' => $request->city,
        ]);

        $user->loadMissing('employer');

        User::query()
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_MOD])
            ->get()
            ->each(fn (User $admin) => $admin->notify(new AdminNewEmployerPending($user->employer)));

        // Send email verification notification
        event(new Registered($user));

        // Redirect to login with success message
        return redirect('/employer/login')->with('message', 
            'Account created successfully! Please verify your email. After verification, your account must be approved before you can post jobs.'
        );
    }
}
