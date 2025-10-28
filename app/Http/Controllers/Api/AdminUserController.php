<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AdminUserController extends Controller
{
    public function __construct()
    {
        // require auth for admin actions; controllers will check is_admin
        $this->middleware('auth:sanctum')->only(['toggle', 'destroy']);
    }

    public function index(Request $request)
    {
        // Return a list of users (id, name, email, is_admin)
        $users = User::select('id', 'name', 'email', 'is_admin', 'created_at')->orderBy('id', 'asc')->get();
        return response()->json($users);
    }

    public function toggle(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Prevent removing admin from self for safety (optional)
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot change your own admin status'], 400);
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return response()->json(['success' => true, 'is_admin' => $user->is_admin]);
    }

    public function destroy(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete yourself'], 400);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}
