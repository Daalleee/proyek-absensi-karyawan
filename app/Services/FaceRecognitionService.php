<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceRecognitionService
{
    protected $apiUrl;
    protected $timeout;

    public function __construct()
    {
        $this->apiUrl = env('FACE_RECOGNITION_API_URL', 'http://localhost:5000');
        $this->timeout = env('FACE_RECOGNITION_API_TIMEOUT', 30);
    }

    /**
     * Verify if the face in the provided image matches a stored face image
     * 
     * @param string $imagePath Path to the face image to verify
     * @param string $storedImagePath Path to the stored face image for comparison
     * @return array
     */
    public function verifyFace(string $imagePath, string $storedImagePath): array
    {
        try {
            // Check if files exist
            if (!file_exists($imagePath) || !file_exists($storedImagePath)) {
                return [
                    'success' => false,
                    'match' => false,
                    'message' => 'Image files not found'
                ];
            }

            // Send request to Python Flask API
            $response = Http::timeout($this->timeout)
                ->attach('image1', file_get_contents($imagePath), 'image1.jpg')
                ->attach('image2', file_get_contents($storedImagePath), 'image2.jpg')
                ->post($this->apiUrl . '/verify');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'match' => $data['match'] ?? false,
                    'confidence' => $data['confidence'] ?? 0,
                    'message' => $data['message'] ?? 'Face verification completed'
                ];
            } else {
                Log::error('Face recognition API error: ' . $response->body());
                return [
                    'success' => false,
                    'match' => false,
                    'message' => 'API request failed: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Face recognition service error: ' . $e->getMessage());
            return [
                'success' => false,
                'match' => false,
                'message' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Register a new face image for an employee
     * 
     * @param string $imagePath Path to the face image to register
     * @param string $employeeId Employee identifier
     * @return array
     */
    public function registerFace(string $imagePath, string $employeeId): array
    {
        try {
            if (!file_exists($imagePath)) {
                return [
                    'success' => false,
                    'message' => 'Image file not found'
                ];
            }

            // Send request to Python Flask API to register the face
            $response = Http::timeout($this->timeout)
                ->attach('image', file_get_contents($imagePath), 'face_image.jpg')
                ->post($this->apiUrl . '/register', [
                    'employee_id' => $employeeId
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => $data['message'] ?? 'Face registered successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'API request failed: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Face registration service error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Detect face in an image
     * 
     * @param string $imagePath Path to the image to analyze
     * @return array
     */
    public function detectFace(string $imagePath): array
    {
        try {
            if (!file_exists($imagePath)) {
                return [
                    'success' => false,
                    'faces' => 0,
                    'message' => 'Image file not found'
                ];
            }

            // Send request to Python Flask API to detect faces
            $response = Http::timeout($this->timeout)
                ->attach('image', file_get_contents($imagePath), 'image.jpg')
                ->post($this->apiUrl . '/detect');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'faces' => $data['faces'] ?? 0,
                    'face_locations' => $data['face_locations'] ?? [],
                    'message' => $data['message'] ?? 'Face detection completed'
                ];
            } else {
                return [
                    'success' => false,
                    'faces' => 0,
                    'message' => 'API request failed: ' . $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Face detection service error: ' . $e->getMessage());
            return [
                'success' => false,
                'faces' => 0,
                'message' => 'Service error: ' . $e->getMessage()
            ];
        }
    }
}