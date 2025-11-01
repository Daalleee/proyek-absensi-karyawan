<?php

namespace App\Http\Controllers;

use App\Models\WorkLocation;
use App\Models\Employee;
use Illuminate\Http\Request;

class WorkLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workLocations = WorkLocation::with('creator')->whereDate('date', '>=', today())->paginate(10);
        return view('admin.work-locations.index', compact('workLocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('role_id', 1) // Assuming role 1 is admin
            ->orWhere('role_id', 2) // Assuming role 2 is field leader
            ->get();
        return view('admin.work-locations.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'status' => 'required|in:active,inactive',
        ]);

        $workLocation = new WorkLocation();
        $workLocation->name = $request->name;
        $workLocation->description = $request->description;
        $workLocation->latitude = $request->latitude;
        $workLocation->longitude = $request->longitude;
        $workLocation->radius = $request->radius;
        $workLocation->date = $request->date;
        $workLocation->start_time = $request->start_time;
        $workLocation->end_time = $request->end_time;
        $workLocation->status = $request->status;
        $workLocation->created_by = auth()->user()->employee->id; // Assuming the authenticated user is creating this
        $workLocation->save();

        return redirect()->route('admin.work-locations.index')->with('success', 'Work location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $workLocation = WorkLocation::with('creator')->findOrFail($id);
        return view('admin.work-locations.show', compact('workLocation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $workLocation = WorkLocation::findOrFail($id);
        $employees = Employee::where('role_id', 1) // admin
            ->orWhere('role_id', 2) // field leader
            ->get();
        return view('admin.work-locations.edit', compact('workLocation', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $workLocation = WorkLocation::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'status' => 'required|in:active,inactive',
        ]);

        $workLocation->update([
            'name' => $request->name,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.work-locations.index')->with('success', 'Work location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workLocation = WorkLocation::findOrFail($id);
        $workLocation->delete();

        return redirect()->route('admin.work-locations.index')->with('success', 'Work location deleted successfully.');
    }
}
