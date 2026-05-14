<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\Setting;
use App\Models\Employer;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Services\ApprovalService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AccessController extends Controller
{
    // -------------------------------------------------------------------------
    // GET /access — renders the correct stage from session
    // -------------------------------------------------------------------------

    public function show(Request $request): View
    {
        $sessionStage = session('access_stage');
        $stage = in_array($sessionStage, ['login', 'verify_code', 'register'], true)
            ? $sessionStage
            : 'email';

        $email      = session('access_email', '');
        $intentType = session(
            'access_intent_type',
            $request->query('type') === User::ROLE_EMPLOYER ? User::ROLE_EMPLOYER : User::ROLE_WORKER
        );

        $devCode = ($this->isDevMode() && $stage === 'verify_code')
            ? session('cw_dev_code')
            : null;

        $canResendImmediately = ($stage === 'verify_code' && $email !== '')
            ? ! Cache::has($this->resendCooldownKey($email))
            : false;

        return view('auth.access', compact('stage', 'email', 'intentType', 'devCode', 'canResendImmediately'));
    }

    // -------------------------------------------------------------------------
    // POST /access/email — check whether email is new or existing
    // -------------------------------------------------------------------------

    public function checkEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'intent_type' => ['nullable', Rule::in([User::ROLE_WORKER, User::ROLE_EMPLOYER])],
        ]);

        $email      = strtolower($data['email']);
        $intentType = $data['intent_type'] ?? User::ROLE_WORKER;

        session(['access_email' => $email, 'access_intent_type' => $intentType]);

        if (User::where('email', $email)->exists()) {
            session(['access_stage' => 'login']);
            return redirect()->route('access.show');
        }

        if (! $this->isRegistrationEnabled($intentType)) {
            session(['access_stage' => 'email']);

            return redirect()->route('access.show')
                ->withErrors(['email' => 'New registrations are currently disabled for this account type.']);
        }

        // New email → send code (respect cooldown in case of double-submit)
        if (! Cache::has($this->resendCooldownKey($email))) {
            $code = $this->sendVerificationCode($email);
            if ($this->isDevMode()) {
                session(['cw_dev_code' => $code]);
            }
        }

        session(['access_stage' => 'verify_code']);
        return redirect()->route('access.show');
    }

    // -------------------------------------------------------------------------
    // POST /access/verify-code — validate the 6-digit code
    // -------------------------------------------------------------------------

    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate([
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'code'        => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
            'intent_type' => ['nullable', Rule::in([User::ROLE_WORKER, User::ROLE_EMPLOYER])],
        ]);

        $email      = strtolower($request->input('email', ''));
        $intentType = $request->input('intent_type', User::ROLE_WORKER);
        $code       = $request->input('code', '');

        session(['access_email' => $email, 'access_intent_type' => $intentType]);

        $cacheKey = $this->verifyCacheKey($email);
        $cached   = Cache::get($cacheKey);

        if (! $cached) {
            session(['access_stage' => 'verify_code']);
            return redirect()->route('access.show')
                ->withErrors(['code' => 'Verification code has expired. Please request a new one.']);
        }

        if ((int) $cached['attempts'] >= 5) {
            Cache::forget($cacheKey);
            session(['access_stage' => 'email', 'access_email' => '']);
            return redirect()->route('access.show')
                ->withErrors(['email' => 'Too many incorrect attempts. Please start over.']);
        }

        if (! hash_equals($cached['hash'], hash('sha256', $code))) {
            $cached['attempts']++;
            $elapsed      = now()->timestamp - (int) $cached['sent_at'];
            $remainingTtl = max(1, 600 - $elapsed);
            Cache::put($cacheKey, $cached, now()->addSeconds($remainingTtl));

            $attemptsLeft = 5 - (int) $cached['attempts'];
            session(['access_stage' => 'verify_code']);
            return redirect()->route('access.show')
                ->withErrors(['code' => "Incorrect code. {$attemptsLeft} attempt(s) remaining."]);
        }

        // ✓ Valid code
        Cache::forget($cacheKey);
        Cache::forget($this->resendCooldownKey($email));
        if ($this->isDevMode()) {
            session()->forget('cw_dev_code');
        }

        session([
            'access_stage'        => 'register',
            'cw_verified_email'   => $email,
        ]);

        return redirect()->route('access.show');
    }

    // -------------------------------------------------------------------------
    // POST /access/resend-code — resend code with cooldown enforcement
    // -------------------------------------------------------------------------

    public function resendCode(Request $request): RedirectResponse
    {
        $request->validate([
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'intent_type' => ['nullable', Rule::in([User::ROLE_WORKER, User::ROLE_EMPLOYER])],
        ]);

        $email      = strtolower($request->input('email', ''));
        $intentType = $request->input('intent_type', User::ROLE_WORKER);

        session(['access_email' => $email, 'access_intent_type' => $intentType, 'access_stage' => 'verify_code']);

        if (Cache::has($this->resendCooldownKey($email))) {
            return redirect()->route('access.show')
                ->withErrors(['resend' => 'Please wait 60 seconds before requesting a new code.']);
        }

        $code = $this->sendVerificationCode($email);

        if ($this->isDevMode()) {
            session(['cw_dev_code' => $code]);
        }

        return redirect()->route('access.show')
            ->with('resend_success', 'A new code has been sent to ' . $email . '.');
    }

    // -------------------------------------------------------------------------
    // POST /access/login
    // -------------------------------------------------------------------------

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $email = strtolower($credentials['email']);

        if (! Auth::attempt(['email' => $email, 'password' => $credentials['password']], (bool) ($credentials['remember'] ?? false))) {
            session(['access_stage' => 'login', 'access_email' => $email]);
            return redirect()->route('access.show')
                ->withErrors(['password' => 'The provided credentials do not match our records.']);
        }

        $request->session()->regenerate();
        session()->forget(['access_stage', 'access_email', 'access_intent_type']);

        $user = Auth::user();
        if ($user->role === User::ROLE_EMPLOYER) {
            return redirect()->intended('/employer');
        }

        return redirect()->intended('/jobs');
    }

    // -------------------------------------------------------------------------
    // POST /access/register
    // -------------------------------------------------------------------------

    public function register(Request $request): RedirectResponse
    {
        // Guard: email must have been verified in this session
        $submittedEmail = strtolower($request->input('email', ''));
        $verifiedEmail  = session('cw_verified_email');

        if (! $verifiedEmail || strtolower($verifiedEmail) !== $submittedEmail) {
            session(['access_stage' => 'email', 'access_email' => '']);
            return redirect()->route('access.show')
                ->withErrors(['email' => 'Email verification is required. Please start again.']);
        }

        $data = $request->validate([
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'name'         => ['required', 'string', 'max:255'],
            'account_type' => ['required', Rule::in([User::ROLE_WORKER, User::ROLE_EMPLOYER])],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (! $this->isRegistrationEnabled($data['account_type'])) {
            session(['access_stage' => 'email', 'access_email' => '']);

            return redirect()->route('access.show')
                ->withErrors(['email' => 'Registrations for this account type are currently disabled.']);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role'     => $data['account_type'],
        ]);

        session()->forget(['cw_verified_email', 'access_stage', 'access_email', 'access_intent_type', 'cw_dev_code']);

        if ($user->role === User::ROLE_WORKER) {
            WorkerProfile::create([
                'user_id'                  => $user->id,
                'first_name'               => '',
                'last_name'                => '',
                'nationality_country_code' => '',
                'birth_year'               => 1940,
                'skills'                   => [],
            ]);

            event(new Registered($user));
            Auth::login($user);

            return redirect()->route('worker.profile.edit');
        }

        $approvalService = app(ApprovalService::class);

        Employer::create([
            'user_id'      => $user->id,
            'company_name' => $user->name,
            'approved_at'  => $approvalService->requiresEmployerApproval() ? null : now(),
        ]);

        event(new Registered($user));

        return redirect()->route('access.show', ['type' => User::ROLE_EMPLOYER])
            ->with('status', 'Account created. Verify your email, then sign in to continue to employer approval.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function sendVerificationCode(string $email): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put(
            $this->verifyCacheKey($email),
            [
                'hash'     => hash('sha256', $code),
                'attempts' => 0,
                'sent_at'  => now()->timestamp,
            ],
            now()->addMinutes(10)
        );

        Cache::put($this->resendCooldownKey($email), true, now()->addSeconds(60));

        Mail::to($email)->send(new VerificationCodeMail($code));

        if ($this->isDevMode()) {
            Log::info("[CroWork Dev] Email verification code for {$email}: {$code}");
        }

        return $code;
    }

    private function verifyCacheKey(string $email): string
    {
        return 'cw_ev_' . hash('sha256', strtolower(trim($email)));
    }

    private function resendCooldownKey(string $email): string
    {
        return 'cw_ev_cd_' . hash('sha256', strtolower(trim($email)));
    }

    private function isDevMode(): bool
    {
        return app()->environment('local') || config('mail.default') === 'log';
    }

    private function isRegistrationEnabled(string $accountType): bool
    {
        if (! Setting::getBool('registration_enabled', true)) {
            return false;
        }

        if ($accountType === User::ROLE_WORKER) {
            return Setting::getBool('worker_registration_enabled', true);
        }

        if ($accountType === User::ROLE_EMPLOYER) {
            return Setting::getBool('employer_registration_enabled', true);
        }

        return false;
    }
}

