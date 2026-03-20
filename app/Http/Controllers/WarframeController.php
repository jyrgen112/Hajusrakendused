<?php

namespace App\Http\Controllers;

use App\Models\WarframeAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class WarframeController extends Controller
{
    public function index(Request $request)
    {
        Cache::remember('warframe_data', 600, function () {
            $this->fetchAndStore();
        });

        $query = WarframeAlert::where('active', true);

        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->faction && $request->faction !== 'all') {
            $query->where('faction', $request->faction);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $sort = $request->sort ?? 'latest';
        if ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'type') {
            $query->orderBy('type');
        }

        $limit = $request->limit ?? 20;
        $alerts = $query->limit($limit)->get();

        return Inertia::render('Warframe/Index', [
            'alerts' => $alerts,
            'filters' => [
                'type' => $request->type ?? 'all',
                'faction' => $request->faction ?? 'all',
                'search' => $request->search ?? '',
                'sort' => $sort,
                'limit' => $limit,
            ]
        ]);
    }

    public function refresh()
    {
        Cache::forget('warframe_data');
        $this->fetchAndStore();
        return redirect()->back();
    }

    private function fetchAndStore()
    {
        try {
            $response = Http::timeout(10)->get('https://api.warframestat.us/pc');
            if (!$response->ok()) return;

            $data = $response->json();

            WarframeAlert::where('active', true)->update(['active' => false]);

            if (!empty($data['alerts'])) {
                foreach ($data['alerts'] as $alert) {
                    $mission = $alert['mission'] ?? [];
                    WarframeAlert::updateOrCreate(
                        ['alert_id' => $alert['id']],
                        [
                            'title' => $mission['type'] ?? 'Alert',
                            'description' => $mission['node'] ?? 'Unknown location',
                            'image' => 'https://warframe.fandom.com/wiki/Special:FilePath/Icon_Grineer.png',
                            'type' => 'alert',
                            'faction' => $mission['faction'] ?? 'Unknown',
                            'eta' => $alert['eta'] ?? null,
                            'active' => true,
                        ]
                    );
                }
            }

            if (!empty($data['events'])) {
                foreach ($data['events'] as $event) {
                    WarframeAlert::updateOrCreate(
                        ['alert_id' => $event['id']],
                        [
                            'title' => $event['description'] ?? 'Event',
                            'description' => $event['tooltip'] ?? $event['description'] ?? 'Active event',
                            'image' => 'https://warframe.fandom.com/wiki/Special:FilePath/WarframeLogoWhite.png',
                            'type' => 'event',
                            'faction' => $event['faction'] ?? 'Tenno',
                            'eta' => $event['eta'] ?? null,
                            'active' => true,
                        ]
                    );
                }
            }

            if (!empty($data['invasions'])) {
                foreach (array_slice($data['invasions'], 0, 20) as $invasion) {
                    WarframeAlert::updateOrCreate(
                        ['alert_id' => $invasion['id']],
                        [
                            'title' => 'Invasion: ' . ($invasion['node'] ?? 'Unknown'),
                            'description' => ($invasion['attackerReward']['asString'] ?? 'No reward') . ' vs ' . ($invasion['defenderReward']['asString'] ?? 'No reward'),
                            'image' => 'https://warframe.fandom.com/wiki/Special:FilePath/Icon_Corpus.png',
                            'type' => 'invasion',
                            'faction' => $invasion['attackingFaction'] ?? 'Unknown',
                            'eta' => $invasion['eta'] ?? null,
                            'active' => !($invasion['completed'] ?? false),
                        ]
                    );
                }
            }

        } catch (\Exception $e) {
            \Log::error('Warframe API error: ' . $e->getMessage());
        }
    }

    public function api(Request $request)
    {
        $query = WarframeAlert::where('active', true);

        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->faction && $request->faction !== 'all') {
            $query->where('faction', $request->faction);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $sort = $request->sort ?? 'latest';
        if ($sort === 'latest') $query->latest();
        elseif ($sort === 'oldest') $query->oldest();
        elseif ($sort === 'type') $query->orderBy('type');

        $limit = $request->limit ?? 20;
        $alerts = $query->limit($limit)->get();

        return response()->json([
            'data' => $alerts,
            'count' => $alerts->count(),
            'filters' => $request->only(['type', 'faction', 'search', 'sort', 'limit']),
        ]);
    }
}