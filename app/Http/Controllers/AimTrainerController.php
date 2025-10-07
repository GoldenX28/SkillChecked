<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AimTrainerResult;
use Illuminate\Support\Facades\Auth;

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
        $sort = $request->get('sort', 'hits');
        $results = AimTrainerResult::with('user')
            ->orderBy($sort, 'desc')
            ->limit(10)
            ->get();
        return response()->json($results);
    }
}