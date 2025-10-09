<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AimTrainerResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AimTrainerController extends Controller
{
    public function store(Request $request)
{
    $result = AimTrainerResult::create([
        'user_id' => Auth::id(),
        'hits' => $request->hits,
        'clicks' => $request->clicks,
        'accuracy' => $request->accuracy,
    ]);
    return response()->json($result);
}

    public function leaderboard(Request $request)
    {
        // Allow only known sortable columns to prevent SQL injection
        $allowed = ['hits', 'clicks', 'accuracy', 'created_at'];
        $sort = $request->get('sort', 'hits');
        if (!in_array($sort, $allowed)) {
            $sort = 'hits';
        }

        // Build a subquery that selects the max value of the chosen column per user
        $sub = DB::table('aim_trainer_results')
            ->select('user_id', DB::raw("MAX($sort) as max_val"))
            ->groupBy('user_id');

        // Join the main table with the subquery to get the row(s) matching each user's best value
        $results = AimTrainerResult::select('aim_trainer_results.*')
            ->joinSub($sub, 'best', function ($join) use ($sort) {
                $join->on('aim_trainer_results.user_id', '=', 'best.user_id')
                     ->whereColumn("aim_trainer_results.$sort", 'best.max_val');
            })
            ->with('user')
            ->orderBy("aim_trainer_results.$sort", 'desc')
            ->limit(10)
            ->get();

        return response()->json($results);
    }
}