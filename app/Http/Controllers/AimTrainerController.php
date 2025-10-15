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
    // Compute accuracy server-side if not provided to ensure consistency
    $hits = (int) $request->hits;
    $clicks = (int) $request->clicks;
    if ($request->has('accuracy') && is_numeric($request->accuracy)) {
        $accuracy = (float) $request->accuracy;
    } else {
        $accuracy = $clicks > 0 ? ($hits / $clicks) * 100 : 0;
        $accuracy = round($accuracy, 2);
    }

    $result = AimTrainerResult::create([
        'user_id' => Auth::id(),
        'hits' => $hits,
        'clicks' => $clicks,
        'accuracy' => $accuracy,
    ]);

    // Attach computed errors for the response (not stored in DB)
    $errors = max(0, $clicks - $hits);
    $result->setAttribute('errors', $errors);

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

        // Add errors (clicks - hits) to each result before returning
        $results->transform(function ($r) {
            $r->setAttribute('errors', max(0, (int) $r->clicks - (int) $r->hits));
            return $r;
        });

        return response()->json($results);
    }
}