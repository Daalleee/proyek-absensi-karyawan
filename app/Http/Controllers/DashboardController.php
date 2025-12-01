<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        if ($user->isAdmin()) {
            $totalEmployees = Employee::count();
            $todayAttendances = Attendance::whereDate('date', $today)->count();
            $presentToday = Attendance::whereDate('date', $today)
                ->whereNotNull('clock_in')
                ->count();
            $lateToday = Attendance::whereDate('date', $today)
                ->where('status', 'late')
                ->count();

            return view('dashboard.admin', compact(
                'totalEmployees',
                'todayAttendances',
                'presentToday',
                'lateToday'
            ));
        }

        $employee = $user->employee;
        $todayAttendance = null;
        
        if ($employee) {
            $todayAttendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();
        }

        $recentAttendances = [];
        if ($employee) {
            $recentAttendances = Attendance::where('employee_id', $employee->id)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();
        }

        return view('dashboard.employee', compact(
            'employee',
            'todayAttendance',
            'recentAttendances'
        ));
    }
}
