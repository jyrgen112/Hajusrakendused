<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class GameController extends Controller
{
    public function index()
    {
        $games = Cache::remember('games_all', 600, function () {
            return Game::orderBy('created_at', 'desc')->get();
        });

        return Inertia::render('Games/Index', [
            'games' => $games,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|url',
            'description' => 'required|string',
            'genre' => 'required|string|max:100',
            'release_year' => 'required|integer|min:1970|max:2030',
        ]);

        Game::create($request->all());
        Cache::forget('games_all');

        return redirect()->back();
    }

    public function update(Request $request, Game $game)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|url',
            'description' => 'required|string',
            'genre' => 'required|string|max:100',
            'release_year' => 'required|integer|min:1970|max:2030',
        ]);

        $game->update($request->all());
        Cache::forget('games_all');

        return redirect()->back();
    }

    public function destroy(Game $game)
    {
        $game->delete();
        Cache::forget('games_all');

        return redirect()->back();
    }

    public function api(Request $request)
    {
        $cacheKey = 'games_api_' . md5(json_encode($request->all()));

        $result = Cache::remember($cacheKey, 600, function () use ($request) {
            $query = Game::query();

            if ($request->search) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            if ($request->genre) {
                $query->where('genre', $request->genre);
            }

            if ($request->sort === 'year') {
                $query->orderBy('release_year', 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $limit = $request->limit ?? 10;
            return $query->limit($limit)->get();
        });

        return response()->json([
            'data' => $result,
            'count' => $result->count(),
        ]);
    }
}