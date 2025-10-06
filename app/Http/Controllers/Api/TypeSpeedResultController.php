<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeSpeedResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeSpeedResultController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'wpm' => 'required|integer',
            'correct_chars' => 'required|integer',
            'errors' => 'required|integer',
            'typed_chars' => 'required|integer',
            'accuracy' => 'required|integer',
        ]);

        // Prevent duplicate save for the same user and stats in a short time window
        $alreadySaved = TypeSpeedResult::where('user_id', Auth::id())
            ->where('wpm', $request->wpm)
            ->where('correct_chars', $request->correct_chars)
            ->where('errors', $request->errors)
            ->where('typed_chars', $request->typed_chars)
            ->where('accuracy', $request->accuracy)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($alreadySaved) {
            return response()->json(['success' => false, 'message' => 'Results already saved.'], 409);
        }

        $result = TypeSpeedResult::create([
            'user_id' => Auth::id(),
            'wpm' => $request->wpm,
            'correct_chars' => $request->correct_chars,
            'errors' => $request->errors,
            'typed_chars' => $request->typed_chars,
            'accuracy' => $request->accuracy,
        ]);

        return response()->json(['success' => true, 'result' => $result], 201);
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
