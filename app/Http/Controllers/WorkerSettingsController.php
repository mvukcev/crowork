<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WorkerSettingsController extends Controller
{
    public function edit()
    {
        $this->ensureWorker();

        return view('worker.settings', [
            'user' => auth()->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $this->ensureWorker();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $request->user()->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('worker.settings.edit')->with('success', 'Settings updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $this->ensureWorker();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('worker.settings.edit')->with('success', 'Password updated successfully.');
    }

    private function ensureWorker(): void
    {
        if (! auth()->user()?->isWorker()) {
            abort(403);
        }
    }
}
