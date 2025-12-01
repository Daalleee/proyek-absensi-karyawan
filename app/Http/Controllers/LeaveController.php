<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $leaves = Leave::with('employee.user', 'approver')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $leaves = Leave::where('employee_id', $user->employee->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sick,annual,permission,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'Data karyawan tidak ditemukan');
        }

        $data = [
            'employee_id' => $employee->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leaves', 'public');
        }

        Leave::create($data);

        return redirect()->route('leaves.index')
            ->with('success', 'Pengajuan izin/cuti berhasil dikirim');
    }

    public function show(string $id)
    {
        $leave = Leave::with('employee.user', 'approver')->findOrFail($id);
        return view('leaves.show', compact('leave'));
    }

    public function approve(Request $request, string $id)
    {
        $leave = Leave::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $leave->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $statusText = $request->status === 'approved' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Pengajuan berhasil {$statusText}");
    }

    public function destroy(string $id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan pending yang dapat dibatalkan');
        }

        $leave->delete();
        return redirect()->route('leaves.index')
            ->with('success', 'Pengajuan berhasil dibatalkan');
    }
}
