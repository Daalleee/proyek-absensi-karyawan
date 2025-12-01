<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Exports\AttendanceReportExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with('user')->get();

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        $employeeId = $request->employee_id;

        $query = Attendance::with('employee.user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->get();

        $summary = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'leave' => $attendances->where('status', 'leave')->count(),
        ];

        return view('reports.index', compact(
            'employees',
            'attendances',
            'summary',
            'startDate',
            'endDate',
            'employeeId'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        $employeeId = $request->employee_id;

        $query = Attendance::with('employee.user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->get();

        $employeeName = null;
        if ($employeeId) {
            $employee = Employee::find($employeeId);
            $employeeName = $employee ? $employee->user->name : null;
        }

        return Excel::download(
            new AttendanceReportExport($attendances, $startDate, $endDate, $employeeName),
            'laporan-absensi-' . $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '.xlsx'
        );
    }
}
