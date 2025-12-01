<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('employees')->paginate(10);
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:5000',
            'description' => 'nullable|string',
        ]);

        Location::create($request->all());

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $location = Location::with('employees.user')->findOrFail($id);
        return view('locations.show', compact('location'));
    }

    public function edit(string $id)
    {
        $location = Location::findOrFail($id);
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, string $id)
    {
        $location = Location::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:5000',
            'description' => 'nullable|string',
        ]);

        $location->update($request->all());

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil dihapus');
    }

    public function toggle(string $id)
    {
        $location = Location::findOrFail($id);
        $location->update(['is_active' => !$location->is_active]);

        $status = $location->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Lokasi berhasil {$status}");
    }
}
