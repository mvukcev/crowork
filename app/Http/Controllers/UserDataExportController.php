<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserDataExportController extends Controller
{
    public function export(Request $request)
    {
        $user = $request->user();

        $data = [
            'user' => $user->toArray(),
            'applications' => $user->jobApplications()->get()->toArray(),
            'comments' => $user->applicationComments()->get()->toArray(),
        ];

        $fileName = 'user_data_' . $user->id . '.json';
        Storage::disk('local')->put($fileName, json_encode($data));

        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }
}