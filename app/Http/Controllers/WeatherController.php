<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->input('city', 'Tallinn');
        $apiKey = config('services.openweather.key');
        $cacheKey = 'weather_' . strtolower($city);

        $weather = Cache::remember($cacheKey, 600, function () use ($city, $apiKey) {
            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
                'lang' => 'en',
            ]);
            return $response->json();
        });

        return Inertia::render('Weather/Index', [
            'weather' => $weather,
            'city' => $city,
        ]);
    }
}