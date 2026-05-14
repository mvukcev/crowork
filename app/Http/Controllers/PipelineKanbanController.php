<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;

class PipelineKanbanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);
        $applications = JobApplication::query()
            ->with(['user', 'job'])
            ->filter($filters)
            ->get()
            ->groupBy('status');

        return response()->json($applications);
    }
}