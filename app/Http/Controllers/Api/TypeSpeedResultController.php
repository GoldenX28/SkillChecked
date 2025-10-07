<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeSpeedResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TypeSpeedResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User must be authenticated'
                ], 401);
            }

            Log::info('TypeSpeed store attempt', [
                'user_id' => Auth::id(),
                'data' => $request->all()
            ]);

            $validated = $request->validate([
                'wpm' => 'required|integer',
                'correct_chars' => 'required|integer',
                'errors' => 'required|integer',
                'typed_chars' => 'required|integer',
                'accuracy' => 'required|numeric'
            ]);

            $result = TypeSpeedResult::create([
                'user_id' => Auth::id(), // Make sure user_id is set
                'wpm' => $validated['wpm'],
                'correct_chars' => $validated['correct_chars'],
                'errors' => $validated['errors'],
                'typed_chars' => $validated['typed_chars'],
                'accuracy' => $validated['accuracy']
            ]);

            return response()->json([
                'success' => true,
                'result' => $result
            ], 201);

        } catch (\Exception $e) {
            Log::error('TypeSpeed store error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save results: ' . $e->getMessage()
            ], 500);
        }
    }

    public function leaderboard(Request $request)
    {
        $count = $request->get('count');
        // Get each user's best result (max wpm)
        $sub = TypeSpeedResult::selectRaw('user_id, MAX(wpm) as max_wpm')
            ->groupBy('user_id');

        $results = TypeSpeedResult::joinSub($sub, 'max_results', function($join) {
            $join->on('type_speed_results.user_id', '=', 'max_results.user_id')
                 ->on('type_speed_results.wpm', '=', 'max_results.max_wpm');
        })
        ->with('user')
        ->orderBy('wpm', 'desc');

        if ($count && is_int($count)) {
            $results = $results->limit($count);
        }

        $results = $results->get();
        return response()->json($results);
    }
}
