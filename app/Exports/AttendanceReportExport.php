<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class AttendanceReportExport implements FromView
{
    use Exportable;

    protected $attendances;
    protected $startDate;
    protected $endDate;
    protected $employeeName;

    public function __construct($attendances, $startDate, $endDate, $employeeName = null)
    {
        $this->attendances = $attendances;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->employeeName = $employeeName;
    }

    public function view(): View
    {
        return view('exports.attendance-report', [
            'attendances' => $this->attendances,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'employeeName' => $this->employeeName,
        ]);
    }
}