<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $fromDate;
    protected $toDate;
    protected $employeeId;

    public function __construct($fromDate, $toDate, $employeeId = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->employeeId = $employeeId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Attendance::with(['employee', 'workLocation'])
            ->whereBetween('check_in_time', [$this->fromDate, $this->toDate . ' 23:59:59']);

        if ($this->employeeId) {
            $query->where('employee_id', $this->employeeId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'Employee Code',
            'Work Location',
            'Check In Time',
            'Check In Coordinates',
            'Check Out Time',
            'Check Out Coordinates',
            'Location Valid (Check In)',
            'Location Valid (Check Out)',
            'Face Recognized',
            'Status',
            'Notes',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->id,
            $attendance->employee->first_name . ' ' . $attendance->employee->last_name,
            $attendance->employee->employee_code,
            $attendance->workLocation->name,
            $attendance->check_in_time ? $attendance->check_in_time->format('Y-m-d H:i:s') : '',
            $attendance->check_in_latitude ? $attendance->check_in_latitude . ', ' . $attendance->check_in_longitude : '',
            $attendance->check_out_time ? $attendance->check_out_time->format('Y-m-d H:i:s') : '',
            $attendance->check_out_latitude ? $attendance->check_out_latitude . ', ' . $attendance->check_out_longitude : '',
            $attendance->is_check_in_valid ? 'Yes' : 'No',
            $attendance->check_out_time ? ($attendance->is_check_out_valid ? 'Yes' : 'No') : 'N/A',
            $attendance->is_face_recognized ? 'Yes' : 'No',
            ucfirst($attendance->status),
            $attendance->notes,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}