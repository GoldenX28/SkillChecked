<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemoryResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MemoryResultController extends Controller
{
    public function __construct()
    {
        // Only require sanctum auth for storing results; leaderboard should be public
        $this->middleware('auth:sanctum')->only('store');
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

            Log::info('Memory store attempt', [
                'user_id' => Auth::id(),
                'data' => $request->all()
            ]);

            $validated = $request->validate([
                'moves' => 'required|integer|min:0',
                'time_seconds' => 'required|numeric|min:0',
                'difficulty' => 'nullable|string|in:easy,normal,hard'
            ]);

            $userId = Auth::id();
            $newMoves = $validated['moves'];
            $newTime = $validated['time_seconds'];

            // Get current best by moves (min moves, tie-breaker min time)
            $bestByMoves = MemoryResult::where('user_id', $userId)
                ->orderBy('moves', 'asc')
                ->orderBy('time_seconds', 'asc')
                ->first();

            // Get current best by time (min time, tie-breaker min moves)
            $bestByTime = MemoryResult::where('user_id', $userId)
                ->orderBy('time_seconds', 'asc')
                ->orderBy('moves', 'asc')
                ->first();

            $isBetterMoves = false;
            $isBetterTime = false;

            if (!$bestByMoves) {
                // No existing runs, accept
                $isBetterMoves = true;
                $isBetterTime = true;
            } else {
                // Compare for moves improvement (moves primary, time tie-breaker)
                if ($newMoves < $bestByMoves->moves) {
                    $isBetterMoves = true;
                } elseif ($newMoves == $bestByMoves->moves && $newTime < $bestByMoves->time_seconds) {
                    $isBetterMoves = true;
                }

                // Compare for time improvement (time primary, moves tie-breaker)
                if (!$bestByTime) {
                    $isBetterTime = true;
                } else {
                    if ($newTime < $bestByTime->time_seconds) {
                        $isBetterTime = true;
                    } elseif ($newTime == $bestByTime->time_seconds && $newMoves < $bestByTime->moves) {
                        $isBetterTime = true;
                    }
                }
            }

            // Save the run to the user's personal history regardless of whether it improved bests.
            // The public leaderboard logic will continue to pick the per-user best runs only.
            $result = MemoryResult::create([
                'user_id' => $userId,
                'moves' => $newMoves,
                'time_seconds' => $newTime,
                'difficulty' => $validated['difficulty'] ?? 'normal',
            ]);

            return response()->json([
                'success' => true,
                'result' => $result,
                'improved' => ($isBetterMoves || $isBetterTime),
                'improved_moves' => $isBetterMoves,
                'improved_time' => $isBetterTime
            ], 201);

        } catch (\Exception $e) {
            Log::error('Memory store error', [
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
        $sort = $request->get('sort', 'moves'); // 'moves' or 'time'
        $difficulty = $request->get('difficulty');
        if ($sort === 'time') {
            // Find each user's best (minimum) time and, for ties, pick the run with minimum moves.
            // Step 1: best_time per user
            $bestTime = MemoryResult::selectRaw('user_id, MIN(time_seconds) as best_time')
                ->when($difficulty, function ($q) use ($difficulty) {
                    $q->where('difficulty', $difficulty);
                })
                ->groupBy('user_id');

            // Step 2: among rows with that best_time, pick the one with min(moves)
            $bestForTime = DB::table('memory_results as m')
                ->when($difficulty, function ($q) use ($difficulty) {
                    $q->where('m.difficulty', $difficulty);
                })
                ->joinSub($bestTime, 'bt', function ($join) {
                    $join->on('m.user_id', '=', 'bt.user_id')
                        ->on('m.time_seconds', '=', 'bt.best_time');
                })
                ->selectRaw('m.user_id, bt.best_time, MIN(m.moves) as best_moves')
                ->groupBy('m.user_id', 'bt.best_time');

        // Join back to memory_results to fetch the full row
        $query = MemoryResult::joinSub($bestForTime, 'best_results', function ($join) {
                    $join->on('memory_results.user_id', '=', 'best_results.user_id')
                        ->on('memory_results.time_seconds', '=', 'best_results.best_time')
                        ->on('memory_results.moves', '=', 'best_results.best_moves');
                })
                ->select('memory_results.*')
                ->with('user')
                ->orderBy('memory_results.time_seconds', 'asc')
                ->orderBy('memory_results.moves', 'asc');
        } else {
            // Default: find each user's best (minimum) moves and return those runs
            // Step 1: best_moves per user
            $bestMoves = MemoryResult::selectRaw('user_id, MIN(moves) as best_moves')
                ->when($difficulty, function ($q) use ($difficulty) {
                    $q->where('difficulty', $difficulty);
                })
                ->groupBy('user_id');

            // Step 2: among rows with that best_moves, pick the one with min(time_seconds)
            $bestForMoves = DB::table('memory_results as m')
                ->when($difficulty, function ($q) use ($difficulty) {
                    $q->where('m.difficulty', $difficulty);
                })
                ->joinSub($bestMoves, 'bm', function ($join) {
                    $join->on('m.user_id', '=', 'bm.user_id')
                        ->on('m.moves', '=', 'bm.best_moves');
                })
                ->selectRaw('m.user_id, bm.best_moves, MIN(m.time_seconds) as best_time')
                ->groupBy('m.user_id', 'bm.best_moves');

            // Join back to memory_results to fetch the full row
            $query = MemoryResult::joinSub($bestForMoves, 'best_results', function ($join) {
                    $join->on('memory_results.user_id', '=', 'best_results.user_id')
                        ->on('memory_results.moves', '=', 'best_results.best_moves')
                        ->on('memory_results.time_seconds', '=', 'best_results.best_time');
                })
                ->select('memory_results.*')
                ->with('user')
                ->orderBy('memory_results.moves', 'asc')
                ->orderBy('memory_results.time_seconds', 'asc');
        }

        if ($difficulty) {
            $query->where('memory_results.difficulty', $difficulty);
        }

        if ($count && is_numeric($count)) {
            $query = $query->limit((int)$count);
        }

        $results = $query->get();

        return response()->json($results);
    }

    /**
     * Return the authenticated user's personal runs, sorted by chosen metric.
     * This returns the user's full saved runs (history) so they can review and optionally
     * display best-by selected category on the frontend.
     */
    public function personal(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([], 401);
        }

        $count = $request->get('count');
        $sort = $request->get('sort', 'moves'); // 'moves' or 'time'

        $userId = Auth::id();

        $query = MemoryResult::where('user_id', $userId)->with('user');

        if ($sort === 'time') {
            $query = $query->orderBy('time_seconds', 'asc')->orderBy('moves', 'asc');
        } else {
            $query = $query->orderBy('moves', 'asc')->orderBy('time_seconds', 'asc');
        }

        if ($count && is_numeric($count)) {
            $query = $query->limit((int)$count);
        }

        $results = $query->get();
        // Determine user's current bests to annotate each run
        $minMovesQuery = MemoryResult::where('user_id', $userId);
        $minTimeQuery = MemoryResult::where('user_id', $userId);
        $difficulty = $request->get('difficulty');
        if ($difficulty) {
            $minMovesQuery->where('difficulty', $difficulty);
            $minTimeQuery->where('difficulty', $difficulty);
        }

        $minMoves = $minMovesQuery->min('moves');
        $minTime = $minTimeQuery->min('time_seconds');

        $payload = $results->map(function ($r) use ($minMoves, $minTime) {
            return [
                'id' => $r->id,
                'moves' => $r->moves,
                'time_seconds' => (float) $r->time_seconds,
                'created_at' => $r->created_at ? $r->created_at->toDateTimeString() : null,
                'is_best_moves' => $r->moves == $minMoves,
                'is_best_time' => $r->time_seconds == $minTime,
            ];
        });

        return response()->json($payload);
    }
}
