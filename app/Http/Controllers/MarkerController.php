<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarkerController extends Controller
{
    public function index()
    {
        return Inertia::render('Map/Index', [
            'markers' => Marker::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        Marker::create($request->all());
        return redirect()->back();
    }

    public function update(Request $request, Marker $marker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $marker->update($request->all());
        return redirect()->back();
    }

    public function destroy(Marker $marker)
    {
        $marker->delete();
        return redirect()->back();
    }
}