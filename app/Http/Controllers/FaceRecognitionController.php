<?php

namespace App\Http\Controllers;

use App\Services\FaceRecognitionService;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceRecognitionController extends Controller
{
    protected $faceRecognitionService;

    public function __construct(FaceRecognitionService $faceRecognitionService)
    {
        $this->faceRecognitionService = $faceRecognitionService;
    }

    /**
     * Verify face during attendance check-in/check-out
     */
    public function verifyFace(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5000', // Max 5MB
        ]);

        $employee = Employee::find($request->employee_id);

        // Check if employee has face image for comparison
        if (!$employee || !$employee->face_image_path) {
            return response()->json([
                'success' => false,
                'match' => false,
                'message' => 'No face image found for this employee'
            ]);
        }

        // Save the uploaded image temporarily
        $uploadedImagePath = $request->file('image')->store('temp', 'public');
        $uploadedImagePath = Storage::disk('public')->path($uploadedImagePath);

        $storedImagePath = Storage::disk('public')->path($employee->face_image_path);

        // Verify the face using the service
        $result = $this->faceRecognitionService->verifyFace($uploadedImagePath, $storedImagePath);

        // Clean up temporary file
        if (file_exists($uploadedImagePath)) {
            unlink($uploadedImagePath);
        }

        return response()->json($result);
    }

    /**
     * Register/update face image for an employee
     */
    public function registerFace(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5000', // Max 5MB
        ]);

        $employee = Employee::find($request->employee_id);

        // Save the face image
        $imagePath = $request->file('image')->store('face-images', 'public');

        $employee->face_image_path = $imagePath;
        $employee->save();

        // Optionally send to Python API for processing
        $result = $this->faceRecognitionService->registerFace(
            Storage::disk('public')->path($imagePath),
            $employee->id
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }

    /**
     * Detect face in an uploaded image
     */
    public function detectFace(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5000', // Max 5MB
        ]);

        // Save the uploaded image temporarily
        $uploadedImagePath = $request->file('image')->store('temp', 'public');
        $uploadedImagePath = Storage::disk('public')->path($uploadedImagePath);

        // Detect face using the service
        $result = $this->faceRecognitionService->detectFace($uploadedImagePath);

        // Clean up temporary file
        if (file_exists($uploadedImagePath)) {
            unlink($uploadedImagePath);
        }

        return response()->json($result);
    }
}
