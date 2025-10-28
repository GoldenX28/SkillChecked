<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MemoryResult;

class AdminMemoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['destroy']);
    }

    public function index(Request $request)
    {
        // optionally filter by difficulty
        $difficulty = $request->get('difficulty');

        $query = MemoryResult::with('user')->orderBy('created_at', 'desc');
        if ($difficulty) {
            $query->where('difficulty', $difficulty);
        }

        $results = $query->limit(200)->get();
        return response()->json($results);
    }

    public function destroy(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $result = MemoryResult::find($id);
        if (!$result) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $result->delete();
        return response()->json(['success' => true]);
    }
}
