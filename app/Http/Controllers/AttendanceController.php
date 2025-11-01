<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\WorkLocation;
use App\Models\Employee;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    protected $faceRecognitionService;

    public function __construct(FaceRecognitionService $faceRecognitionService)
    {
        $this->faceRecognitionService = $faceRecognitionService;
    }

    /**
     * Store a newly created check-in attendance in storage.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5000', // Face image
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        // Check if there's already an active check-in without check-out
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereNull('check_out_time')
            ->whereDate('check_in_time', today())
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'You are already checked in today. Please check out first.'
            ], 400);
        }

        // Find today's work location
        $workLocation = WorkLocation::where('date', today())
            ->where('status', 'active')
            ->first();

        if (!$workLocation) {
            return response()->json([
                'success' => false,
                'message' => 'No work location set for today.'
            ], 400);
        }

        // Validate location (Haversine formula)
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $workLocation->latitude,
            $workLocation->longitude
        );

        $isLocationValid = $distance <= $workLocation->radius;

        // Save the face image
        $imagePath = $request->file('image')->store('attendance-images', 'public');

        // Create attendance record
        $attendance = new Attendance();
        $attendance->employee_id = $employee->id;
        $attendance->work_location_id = $workLocation->id;
        $attendance->check_in_time = now();
        $attendance->check_in_latitude = $request->latitude;
        $attendance->check_in_longitude = $request->longitude;
        $attendance->check_in_image_path = $imagePath;
        $attendance->is_check_in_valid = $isLocationValid;
        $attendance->is_face_recognized = false; // Will be updated after face verification
        $attendance->status = 'pending';
        $attendance->save();

        // Perform face recognition (async to not block the response)
        $this->performFaceRecognition($attendance, $imagePath);

        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully',
            'attendance_id' => $attendance->id,
            'location_valid' => $isLocationValid,
            'distance' => round($distance, 2)
        ]);
    }

    /**
     * Update the specified check-out attendance in storage.
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5000', // Face image
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        // Get the attendance record
        $attendance = Attendance::find($request->attendance_id);

        // Verify this attendance belongs to the current user
        if ($attendance->employee_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this attendance record.'
            ], 403);
        }

        // Can't check out twice
        if ($attendance->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked out for this session.'
            ], 400);
        }

        // Find today's work location
        $workLocation = WorkLocation::where('date', today())
            ->where('status', 'active')
            ->first();

        // Validate location for check-out
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $workLocation->latitude,
            $workLocation->longitude
        );

        $isLocationValid = $distance <= $workLocation->radius;

        // Save the face image
        $imagePath = $request->file('image')->store('attendance-images', 'public');

        // Update attendance record
        $attendance->check_out_time = now();
        $attendance->check_out_latitude = $request->latitude;
        $attendance->check_out_longitude = $request->longitude;
        $attendance->check_out_image_path = $imagePath;
        $attendance->is_check_out_valid = $isLocationValid;
        $attendance->status = 'completed'; // Update status to completed after check-out
        $attendance->save();

        // Perform face recognition for check-out too
        $this->performFaceRecognitionOnCheckout($attendance, $imagePath);

        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully',
            'location_valid' => $isLocationValid,
            'distance' => round($distance, 2)
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371e3; // Earth's radius in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * 
            pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    /**
     * Perform face recognition for check-in
     */
    private function performFaceRecognition($attendance, $imagePath)
    {
        // Run face recognition in background to not block the response
        dispatch(function () use ($attendance, $imagePath) {
            $employee = $attendance->employee;
            
            // Check if employee has a stored face image
            if (!$employee->face_image_path) {
                // If no stored face, mark as not recognized
                $attendance->is_face_recognized = false;
                $attendance->save();
                return;
            }

            // Verify the face using the service
            $storedImagePath = Storage::disk('public')->path($employee->face_image_path);
            $uploadedImagePath = Storage::disk('public')->path($imagePath);

            $result = $this->faceRecognitionService->verifyFace($uploadedImagePath, $storedImagePath);

            $attendance->is_face_recognized = $result['match'] ?? false;
            $attendance->status = $result['match'] ? 'approved' : 'pending'; // Or 'rejected' depending on requirements
            $attendance->save();
        });
    }

    /**
     * Perform face recognition for check-out
     */
    private function performFaceRecognitionOnCheckout($attendance, $imagePath)
    {
        // Run face recognition in background to not block the response
        dispatch(function () use ($attendance, $imagePath) {
            $employee = $attendance->employee;
            
            // Check if employee has a stored face image
            if (!$employee->face_image_path) {
                return; // If no stored face, we can't verify, but this doesn't necessarily invalidate the checkout
            }

            // Verify the face using the service
            $storedImagePath = Storage::disk('public')->path($employee->face_image_path);
            $uploadedImagePath = Storage::disk('public')->path($imagePath);

            $result = $this->faceRecognitionService->verifyFace($uploadedImagePath, $storedImagePath);
            
            // For check-out, we might want to update the record with face recognition result
            // But for now, we'll just log it or handle as needed
            if ($result['success']) {
                // The face matched during check-out too
            }
        });
    }

    /**
     * Get employee's attendance history
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $fromDate = $request->input('from_date', today()->subDays(30)->toDateString());
        $toDate = $request->input('to_date', today()->toDateString());

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('check_in_time', [$fromDate, $toDate])
            ->with(['workLocation', 'employee'])
            ->orderBy('check_in_time', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'attendances' => $attendances
        ]);
    }

    /**
     * Get today's attendance status
     */
    public function todayStatus()
    {
        $user = Auth::user();
        $employee = $user->employee;

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('check_in_time', today())
            ->latest()
            ->first();

        $workLocation = WorkLocation::where('date', today())
            ->where('status', 'active')
            ->first();

        return response()->json([
            'has_checked_in' => $todayAttendance ? true : false,
            'has_checked_out' => $todayAttendance ? ($todayAttendance->check_out_time ? true : false) : false,
            'work_location' => $workLocation,
            'attendance' => $todayAttendance
        ]);
    }

    /**
     * Display a listing of attendances for admin/field leader
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['employee', 'workLocation']);

        // Add filters based on request
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        if ($request->has('date')) {
            $query->whereDate('check_in_time', $request->date);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest()->paginate(15);

        return view('admin.attendances.index', compact('attendances'));
    }

    /**
     * Display the specified attendance.
     */
    public function show($id)
    {
        $attendance = Attendance::with(['employee', 'workLocation'])->findOrFail($id);
        return view('admin.attendances.show', compact('attendance'));
    }

    /**
     * Generate attendance report
     */
    public function report(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $query = Attendance::with(['employee', 'workLocation'])
            ->whereBetween('check_in_time', [$request->from_date, $request->to_date . ' 23:59:59']);

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->get();

        // Calculate statistics
        $totalAttendances = $attendances->count();
        $validLocationCount = $attendances->where('is_check_in_valid', true)->count();
        $faceRecognizedCount = $attendances->where('is_face_recognized', true)->count();
        $completedAttendances = $attendances->where('status', 'completed')->count();

        // Group by employee for detailed report
        $reportData = $attendances->groupBy('employee_id')->map(function ($employeeAttendances) {
            return [
                'employee' => $employeeAttendances->first()->employee,
                'total_check_ins' => $employeeAttendances->count(),
                'valid_check_ins' => $employeeAttendances->where('is_check_in_valid', true)->count(),
                'face_recognized' => $employeeAttendances->where('is_face_recognized', true)->count(),
                'total_work_hours' => $employeeAttendances->sum(function ($attendance) {
                    if ($attendance->check_in_time && $attendance->check_out_time) {
                        return $attendance->check_in_time->diffInHours($attendance->check_out_time);
                    }
                    return 0;
                })
            ];
        });

        return response()->json([
            'success' => true,
            'summary' => [
                'total_attendances' => $totalAttendances,
                'valid_location_attendances' => $validLocationCount,
                'face_recognized_attendances' => $faceRecognizedCount,
                'completed_attendances' => $completedAttendances,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
            ],
            'detailed_report' => $reportData,
        ]);
    }

    /**
     * Export attendance report to Excel
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $employeeId = $request->employee_id;

        return (new \App\Exports\AttendanceExport($fromDate, $toDate, $employeeId))
            ->download('attendance_report_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export attendance report to PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $query = Attendance::with(['employee', 'workLocation'])
            ->whereBetween('check_in_time', [$request->from_date, $request->to_date . ' 23:59:59']);

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->get();
        
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $pdf = \PDF::loadView('admin.attendances.pdf-report', [
            'attendances' => $attendances,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);

        return $pdf->download('attendance_report_' . date('Y-m-d') . '.pdf');
    }
}
