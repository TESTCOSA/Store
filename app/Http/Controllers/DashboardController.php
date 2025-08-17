<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Customize your dashboard data as needed.
        $data = [
            'username'  => $user->username,
            'email'     => $user->email,
            'full_name' => $user->userDetails->full_name_en,
            // Add more data here if required.
        ];

        return response()->json($data);
    }
}
