<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Patient search API - Protected with auth and rate limiting
Route::middleware(['auth:sanctum', 'throttle:30,1'])->get('/patients/search', function (Request $request) {
    $phone = $request->query('phone');

    // Validate phone format
    if (!$phone || strlen($phone) < 9 || strlen($phone) > 15) {
        return response()->json(['patient' => null, 'error' => 'Invalid phone format'], 400);
    }

    // Only allow searching within user's branch
    $branchId = auth()->user()->branch_id;

    $patient = \App\Models\Patient::where('phone', $phone)
        ->when($branchId, function($query) use ($branchId) {
            return $query->where('first_visit_branch_id', $branchId);
        })
        ->first();

    if ($patient) {
        return response()->json([
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
                'phone' => $patient->phone
            ]
        ]);
    }

    return response()->json(['patient' => null]);
});
