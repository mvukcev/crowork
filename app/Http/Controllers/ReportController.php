<?php

namespace App\Http\Controllers;

use App\Models\AbuseReport;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:job'],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $target = $this->resolveTarget($validated['type'], (int) $validated['id']);
        if (! $target) {
            abort(404);
        }

        return view('reports.create', [
            'type' => $validated['type'],
            'targetId' => (int) $validated['id'],
            'targetTitle' => $target->title ?? 'Listing',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:job'],
            'id' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'in:spam,scam,fake,misleading,inappropriate,other'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $target = $this->resolveTarget($validated['type'], (int) $validated['id']);
        if (! $target) {
            abort(404);
        }

        if (! Schema::hasTable('abuse_reports')) {
            return redirect()->back()->with('error', 'Reporting is temporarily unavailable.');
        }

        AbuseReport::create([
            'type' => $validated['type'],
            'target_id' => (int) $validated['id'],
            'reason' => $validated['reason'],
            'message' => $validated['message'] ?? null,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            'status' => 'open',
        ]);

        if ($validated['type'] === 'job') {
            return redirect()->route('jobs.show', $target)->with('success', 'Thanks. Your report was submitted.');
        }

        return redirect()->route('home')->with('success', 'Thanks. Your report was submitted.');
    }

    private function resolveTarget(string $type, int $id): ?object
    {
        return match ($type) {
            'job' => Job::query()->find($id),
            default => null,
        };
    }
}
