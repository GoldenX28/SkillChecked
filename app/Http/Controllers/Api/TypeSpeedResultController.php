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
}
