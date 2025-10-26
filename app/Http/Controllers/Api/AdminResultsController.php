<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MemoryResult;
use App\Models\TypeSpeedResult;
use App\Models\AimTrainerResult;

class AdminResultsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['destroy']);
    }

    /**
     * Return results across games or for a specific game.
     * Query params: game (memory|typespeed|aimtrainer), sort_by, order (asc|desc)
     */
    public function index(Request $request)
    {
        $game = $request->get('game');
        $sortBy = $request->get('sort_by');
        $order = $request->get('order', 'desc');

        if ($game === 'memory') {
            $query = MemoryResult::with('user');
            if ($sortBy) {
                $query->orderBy($sortBy, $order);
            } else {
                $query->orderBy('created_at', 'desc');
            }
            $rows = $query->limit(500)->get()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'game' => 'memory',
                    'user' => $r->user,
                    'user_id' => $r->user_id,
                    'moves' => $r->moves,
                    'time_seconds' => $r->time_seconds,
                    'difficulty' => $r->difficulty,
                    'created_at' => $r->created_at,
                ];
            });
            return response()->json($rows);
        }

        if ($game === 'typespeed') {
            $query = TypeSpeedResult::with('user');
            if ($sortBy) {
                $query->orderBy($sortBy, $order);
            } else {
                $query->orderBy('created_at', 'desc');
            }
            $rows = $query->limit(500)->get()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'game' => 'typespeed',
                    'user' => $r->user,
                    'user_id' => $r->user_id,
                    'wpm' => $r->wpm,
                    'accuracy' => $r->accuracy,
                    'created_at' => $r->created_at,
                ];
            });
            return response()->json($rows);
        }

        if ($game === 'aimtrainer') {
            $query = AimTrainerResult::with('user');
            if ($sortBy) {
                $query->orderBy($sortBy, $order);
            } else {
                $query->orderBy('created_at', 'desc');
            }
            $rows = $query->limit(500)->get()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'game' => 'aimtrainer',
                    'user' => $r->user,
                    'user_id' => $r->user_id,
                    'hits' => $r->hits,
                    'accuracy' => $r->accuracy,
                    'created_at' => $r->created_at,
                ];
            });
            return response()->json($rows);
        }

        // Combined - return latest entries across all games ordered by created_at
        $mem = MemoryResult::with('user')->limit(200)->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'game' => 'memory',
                'user' => $r->user,
                'user_id' => $r->user_id,
                'moves' => $r->moves,
                'time_seconds' => $r->time_seconds,
                'difficulty' => $r->difficulty,
                'created_at' => $r->created_at,
            ];
        });

        $type = TypeSpeedResult::with('user')->limit(200)->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'game' => 'typespeed',
                'user' => $r->user,
                'user_id' => $r->user_id,
                'wpm' => $r->wpm,
                'accuracy' => $r->accuracy,
                'created_at' => $r->created_at,
            ];
        });

        $aim = AimTrainerResult::with('user')->limit(200)->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'game' => 'aimtrainer',
                'user' => $r->user,
                'user_id' => $r->user_id,
                'hits' => $r->hits,
                'accuracy' => $r->accuracy,
                'created_at' => $r->created_at,
            ];
        });

        $combined = $mem->concat($type)->concat($aim)->sortByDesc('created_at')->values();
        return response()->json($combined);
    }

    /**
     * Delete a result. Route: DELETE /api/admin/results/{game}/{id}
     */
    public function destroy(Request $request, $game, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($game === 'memory') {
            $m = MemoryResult::find($id);
            if (!$m) return response()->json(['message' => 'Not found'], 404);
            $m->delete();
            return response()->json(['success' => true]);
        }

        if ($game === 'typespeed') {
            $r = TypeSpeedResult::find($id);
            if (!$r) return response()->json(['message' => 'Not found'], 404);
            $r->delete();
            return response()->json(['success' => true]);
        }

        if ($game === 'aimtrainer') {
            $r = AimTrainerResult::find($id);
            if (!$r) return response()->json(['message' => 'Not found'], 404);
            $r->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['message' => 'Invalid game'], 400);
    }
}
