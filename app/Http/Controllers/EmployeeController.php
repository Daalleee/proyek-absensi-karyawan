<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->paginate(10);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'employee_id' => 'required|unique:employees',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        Employee::create([
            'user_id' => $user->id,
            'employee_id' => $request->employee_id,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'department' => $request->department,
            'position' => $request->position,
            'hire_date' => $request->hire_date,
            'address' => $request->address,
            'photo' => $photoPath,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $employee = Employee::with('user', 'attendances')->findOrFail($id);
        return view('employees.show', compact('employee'));
    }

    public function edit(string $id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'employee_id' => 'required|unique:employees,employee_id,' . $id,
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $employee->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $employee->user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Handle photo upload
        $photoPath = $employee->photo;
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($employee->photo && file_exists(public_path('storage/' . $employee->photo))) {
                unlink(public_path('storage/' . $employee->photo));
            }

            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        $employee->update([
            'employee_id' => $request->employee_id,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'department' => $request->department,
            'position' => $request->position,
            'hire_date' => $request->hire_date,
            'address' => $request->address,
            'photo' => $photoPath,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->user->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil dihapus');
    }
}
