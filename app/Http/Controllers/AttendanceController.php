<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $attendances = Attendance::with('employee.user', 'location')
                ->orderBy('date', 'desc')
                ->paginate(15);
        } else {
            $attendances = Attendance::where('employee_id', $user->employee->id)
                ->with('location')
                ->orderBy('date', 'desc')
                ->paginate(15);
        }

        return view('attendances.index', compact('attendances'));
    }

    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 400);
        }

        $today = Carbon::today();
        $now = Carbon::now();
        
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance && $attendance->clock_in) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan clock in hari ini'], 400);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $locationId = null;
        $distance = null;

        $assignedLocation = $employee->assignedLocation;
        if ($assignedLocation && $assignedLocation->is_active) {
            $distance = $this->calculateDistance(
                $latitude, $longitude,
                $assignedLocation->latitude, $assignedLocation->longitude
            );

            if ($distance > $assignedLocation->radius) {
                return response()->json([
                    'success' => false,
                    'message' => "Anda berada di luar radius lokasi kerja ({$distance}m dari lokasi, maksimal {$assignedLocation->radius}m)"
                ], 400);
            }
            $locationId = $assignedLocation->id;
        }

        $status = 'present';
        $lateTime = Carbon::today()->setTime(8, 30, 0);
        if ($now->gt($lateTime)) {
            $status = 'late';
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance-photos', 'public');
        } elseif ($request->photo_base64) {
            $photoPath = $this->saveBase64Image($request->photo_base64, 'attendance-photos');
        }

        $data = [
            'clock_in' => $now->format('H:i:s'),
            'clock_in_latitude' => $latitude,
            'clock_in_longitude' => $longitude,
            'clock_in_distance' => $distance ? round($distance) : null,
            'clock_in_photo' => $photoPath,
            'location_id' => $locationId,
            'status' => $status,
        ];

        if ($attendance) {
            $attendance->update($data);
        } else {
            $data['employee_id'] = $employee->id;
            $data['date'] = $today;
            Attendance::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Clock In berhasil pada ' . $now->format('H:i:s'),
            'time' => $now->format('H:i:s'),
            'distance' => $distance ? round($distance) . 'm' : null,
        ]);
    }

    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan'], 400);
        }

        $today = Carbon::today();
        $now = Carbon::now();
        
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return response()->json(['success' => false, 'message' => 'Anda belum melakukan clock in hari ini'], 400);
        }

        if ($attendance->clock_out) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan clock out hari ini'], 400);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $distance = null;

        $assignedLocation = $employee->assignedLocation;
        if ($assignedLocation && $assignedLocation->is_active) {
            $distance = $this->calculateDistance(
                $latitude, $longitude,
                $assignedLocation->latitude, $assignedLocation->longitude
            );
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance-photos', 'public');
        } elseif ($request->photo_base64) {
            $photoPath = $this->saveBase64Image($request->photo_base64, 'attendance-photos');
        }

        $attendance->update([
            'clock_out' => $now->format('H:i:s'),
            'clock_out_latitude' => $latitude,
            'clock_out_longitude' => $longitude,
            'clock_out_distance' => $distance ? round($distance) : null,
            'clock_out_photo' => $photoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clock Out berhasil pada ' . $now->format('H:i:s'),
            'time' => $now->format('H:i:s'),
        ]);
    }

    public function todayList()
    {
        $today = Carbon::today();
        $attendances = Attendance::with('employee.user', 'location')
            ->whereDate('date', $today)
            ->get();
        
        $employees = Employee::with('user', 'assignedLocation')->get();
        
        return view('attendances.today', compact('attendances', 'employees'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function saveBase64Image($base64, $folder)
    {
        $image = str_replace('data:image/jpeg;base64,', '', $base64);
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        
        $imageName = $folder . '/' . uniqid() . '.jpg';
        Storage::disk('public')->put($imageName, base64_decode($image));
        
        return $imageName;
    }
}
