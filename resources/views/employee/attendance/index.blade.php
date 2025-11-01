<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendance Check') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Attendance System</h1>
                    
                    <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Today's Work Location</h2>
                        @php
                            $todayWorkLocation = \App\Models\WorkLocation::where('date', today())->first();
                            $todayAttendance = \App\Models\Attendance::where('employee_id', Auth::user()->employee->id)
                                ->whereDate('check_in_time', today())
                                ->latest()
                                ->first();
                        @endphp
                        
                        @if($todayWorkLocation)
                            <div class="mb-4">
                                <p class="font-semibold">{{ $todayWorkLocation->name }}</p>
                                <p class="text-sm text-gray-600">{{ $todayWorkLocation->description }}</p>
                                <p class="text-sm mt-1">Coordinates: {{ $todayWorkLocation->latitude }}, {{ $todayWorkLocation->longitude }}</p>
                                <p class="text-sm">Radius: {{ $todayWorkLocation->radius }} meters</p>
                            </div>
                            
                            <div id="location-validation" class="mt-4">
                                <p class="text-sm">Checking your location...</p>
                                <div id="location-status" class="mt-2"></div>
                            </div>
                        @else
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <p class="text-yellow-800">No work location assigned for today. Please contact your supervisor.</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($todayWorkLocation)
                    <!-- Status indicator -->
                    <div class="mb-6 p-4 rounded-lg bg-blue-50">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-semibold">Attendance Status:</p>
                                @if($todayAttendance && $todayAttendance->check_out_time)
                                    <p class="text-green-600">✓ Checked out for today</p>
                                @elseif($todayAttendance)
                                    <p class="text-yellow-600">⚠ Checked in, not yet checked out</p>
                                @else
                                    <p class="text-blue-600">○ Not checked in yet today</p>
                                @endif
                            </div>
                            <div>
                                <span id="current-time-full" class="text-lg font-semibold">{{ now()->format('H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Check In Section -->
                        <div class="border rounded-lg p-6 @if($todayAttendance && $todayAttendance->check_in_time) bg-gray-100 @endif">
                            <h3 class="text-lg font-semibold mb-4">Check In</h3>
                            
                            <div class="mb-4">
                                <p class="text-sm">Current Time: <span id="current-time-in">{{ now()->format('H:i:s') }}</span></p>
                            </div>
                            
                            <div class="mb-4">
                                <button id="take-photo-in" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:bg-gray-400" @if($todayAttendance && $todayAttendance->check_in_time) disabled @endif>
                                    Take Photo
                                </button>
                                <div id="photo-container-in" class="mt-4"></div>
                                <video id="video-in" width="320" height="240" class="mt-2 border rounded" style="display: none;"></video>
                                <canvas id="canvas-in" style="display: none;"></canvas>
                            </div>
                            
                            <div class="mb-4">
                                <button id="check-in-btn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 disabled:bg-gray-400" @if($todayAttendance && $todayAttendance->check_in_time) disabled @endif>
                                    Check In
                                </button>
                                <span id="check-in-status" class="ml-2"></span>
                            </div>
                        </div>
                        
                        <!-- Check Out Section -->
                        <div class="border rounded-lg p-6 @if(!($todayAttendance && $todayAttendance->check_in_time) || ($todayAttendance && $todayAttendance->check_out_time)) bg-gray-100 @endif">
                            <h3 class="text-lg font-semibold mb-4">Check Out</h3>
                            
                            <div class="mb-4">
                                <p class="text-sm">Current Time: <span id="current-time-out">{{ now()->format('H:i:s') }}</span></p>
                            </div>
                            
                            <div class="mb-4">
                                <button id="take-photo-out" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:bg-gray-400" @if(!($todayAttendance && $todayAttendance->check_in_time) || ($todayAttendance && $todayAttendance->check_out_time)) disabled @endif>
                                    Take Photo
                                </button>
                                <div id="photo-container-out" class="mt-4"></div>
                                <video id="video-out" width="320" height="240" class="mt-2 border rounded" style="display: none;"></video>
                                <canvas id="canvas-out" style="display: none;"></canvas>
                            </div>
                            
                            <div class="mb-4">
                                <button id="check-out-btn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 disabled:bg-gray-400" @if(!($todayAttendance && $todayAttendance->check_in_time) || ($todayAttendance && $todayAttendance->check_out_time)) disabled @endif>
                                    Check Out
                                </button>
                                <span id="check-out-status" class="ml-2"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Recent Attendance History</h3>
                        @php
                            $recentAttendances = \App\Models\Attendance::where('employee_id', Auth::user()->employee->id)
                                ->orderBy('check_in_time', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recentAttendances as $attendance)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->check_in_time->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->check_in_time->format('H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : 'Not checked out' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($attendance->check_out_time) bg-green-100 text-green-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ $attendance->check_out_time ? 'Completed' : 'In Progress' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">No recent attendance records</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript for camera and location functionality -->
    <script>
        // CSRF token for API requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Update time every second
        setInterval(() => {
            const currentTime = new Date().toLocaleTimeString();
            document.getElementById('current-time-in').textContent = currentTime;
            document.getElementById('current-time-out').textContent = currentTime;
            document.getElementById('current-time-full').textContent = currentTime;
        }, 1000);
        
        // Get location and validate against work location
        function validateLocation() {
            const locationStatus = document.getElementById('location-status');
            
            if (navigator.geolocation) {
                locationStatus.textContent = 'Getting your location...';
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        const accuracy = position.coords.accuracy; // GPS accuracy in meters
                        
                        // Get today's work location
                        @if($todayWorkLocation)
                        const workLat = {{ $todayWorkLocation->latitude }};
                        const workLng = {{ $todayWorkLocation->longitude }};
                        const radius = {{ $todayWorkLocation->radius }};
                        
                        // Calculate distance using Haversine formula
                        const distance = calculateDistance(userLat, userLng, workLat, workLng);
                        
                        if (distance <= radius) {
                            locationStatus.innerHTML = `<span class="text-green-600">✓ You are at the work location (Distance: ${Math.round(distance)}m, Accuracy: ${Math.round(accuracy)}m)</span>`;
                            // Enable check in/out buttons only if not already done
                            @if(!$todayAttendance || !$todayAttendance->check_in_time)
                            document.getElementById('check-in-btn').disabled = false;
                            @endif
                            @if($todayAttendance && $todayAttendance->check_in_time && !$todayAttendance->check_out_time)
                            document.getElementById('check-out-btn').disabled = false;
                            @endif
                        } else {
                            locationStatus.innerHTML = `<span class="text-red-600">✗ You are not at the work location (Distance: ${Math.round(distance)}m, Accuracy: ${Math.round(accuracy)}m)</span>`;
                            // Disable buttons if location is invalid
                            document.getElementById('check-in-btn').disabled = true;
                            document.getElementById('check-out-btn').disabled = true;
                        }
                        @endif
                    },
                    function(error) {
                        let errorMessage = '';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = "User denied the request for Geolocation.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = "Location information is unavailable.";
                                break;
                            case error.TIMEOUT:
                                errorMessage = "The request to get user location timed out.";
                                break;
                            case error.UNKNOWN_ERROR:
                                errorMessage = "An unknown error occurred.";
                                break;
                        }
                        locationStatus.innerHTML = `<span class="text-red-600">✗ Could not get your location: ${errorMessage}</span>`;
                        // Disable buttons if location access fails
                        document.getElementById('check-in-btn').disabled = true;
                        document.getElementById('check-out-btn').disabled = true;
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000, // 10 seconds
                        maximumAge: 0
                    }
                );
            } else {
                locationStatus.innerHTML = '<span class="text-red-600">✗ Geolocation is not supported by this browser.</span>';
                document.getElementById('check-in-btn').disabled = true;
                document.getElementById('check-out-btn').disabled = true;
            }
        }
        
        // Haversine formula to calculate distance between two coordinates
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // Earth's radius in meters
            const φ1 = lat1 * Math.PI/180;
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lon2-lon1) * Math.PI/180;

            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

            return R * c; // Distance in meters
        }
        
        // Camera functionality for check in
        document.getElementById('take-photo-in').addEventListener('click', function() {
            const video = document.getElementById('video-in');
            const canvas = document.getElementById('canvas-in');
            const photoContainer = document.getElementById('photo-container-in');
            
            // Show video element
            video.style.display = 'block';
            
            // Access the camera
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    video.srcObject = stream;
                    
                    // Take photo after a delay to allow camera to load
                    setTimeout(function() {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                        
                        // Stop camera stream
                        stream.getTracks().forEach(track => track.stop());
                        
                        // Display the captured image
                        const img = document.createElement('img');
                        img.src = canvas.toDataURL('image/png');
                        img.className = 'max-w-xs border rounded';
                        photoContainer.innerHTML = '';
                        photoContainer.appendChild(img);
                        
                        // Hide video after capturing
                        video.style.display = 'none';
                    }, 500);
                })
                .catch(function(err) {
                    console.error("An error occurred: " + err);
                    photoContainer.innerHTML = '<p class="text-red-500">Could not access camera: ' + err.message + '</p>';
                });
        });
        
        // Camera functionality for check out
        document.getElementById('take-photo-out').addEventListener('click', function() {
            const video = document.getElementById('video-out');
            const canvas = document.getElementById('canvas-out');
            const photoContainer = document.getElementById('photo-container-out');
            
            // Show video element
            video.style.display = 'block';
            
            // Access the camera
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    video.srcObject = stream;
                    
                    // Take photo after a delay to allow camera to load
                    setTimeout(function() {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                        
                        // Stop camera stream
                        stream.getTracks().forEach(track => track.stop());
                        
                        // Display the captured image
                        const img = document.createElement('img');
                        img.src = canvas.toDataURL('image/png');
                        img.className = 'max-w-xs border rounded';
                        photoContainer.innerHTML = '';
                        photoContainer.appendChild(img);
                        
                        // Hide video after capturing
                        video.style.display = 'none';
                    }, 500);
                })
                .catch(function(err) {
                    console.error("An error occurred: " + err);
                    photoContainer.innerHTML = '<p class="text-red-500">Could not access camera: ' + err.message + '</p>';
                });
        });
        
        // Check in button functionality
        document.getElementById('check-in-btn').addEventListener('click', function() {
            const status = document.getElementById('check-in-status');
            const photoContainer = document.getElementById('photo-container-in');
            
            if (photoContainer.innerHTML.trim() === '') {
                status.innerHTML = '<span class="text-red-600">Please take a photo first!</span>';
                return;
            }
            
            // Get the image data from the canvas
            const img = photoContainer.querySelector('img');
            if (!img) {
                status.innerHTML = '<span class="text-red-600">No photo available!</span>';
                return;
            }
            
            status.innerHTML = '<span class="text-blue-600">Processing check in...</span>';
            
            // Get current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Create a FormData object to send image and location data
                        const formData = new FormData();
                        formData.append('latitude', position.coords.latitude);
                        formData.append('longitude', position.coords.longitude);
                        formData.append('image', dataURLtoFile(img.src, 'check_in_photo.jpg'));
                        
                        // Send the data to the server
                        fetch('{{ route("attendance.check-in") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                status.innerHTML = '<span class="text-green-600">✓ Checked in successfully!</span>';
                                // Disable the check-in button
                                document.getElementById('check-in-btn').disabled = true;
                                document.getElementById('take-photo-in').disabled = true;
                            } else {
                                status.innerHTML = `<span class="text-red-600">✗ ${data.message}</span>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            status.innerHTML = '<span class="text-red-600">✗ An error occurred while checking in.</span>';
                        });
                    },
                    function(error) {
                        status.innerHTML = '<span class="text-red-600">✗ Could not get location for check-in.</span>';
                    }
                );
            } else {
                status.innerHTML = '<span class="text-red-600">✗ Geolocation is not supported.</span>';
            }
        });
        
        // Check out button functionality
        document.getElementById('check-out-btn').addEventListener('click', function() {
            const status = document.getElementById('check-out-status');
            const photoContainer = document.getElementById('photo-container-out');
            
            if (photoContainer.innerHTML.trim() === '') {
                status.innerHTML = '<span class="text-red-600">Please take a photo first!</span>';
                return;
            }
            
            // Get the image data from the canvas
            const img = photoContainer.querySelector('img');
            if (!img) {
                status.innerHTML = '<span class="text-red-600">No photo available!</span>';
                return;
            }
            
            status.innerHTML = '<span class="text-blue-600">Processing check out...</span>';
            
            // Get current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Find today's attendance record
                        @if($todayAttendance)
                        // Create a FormData object to send image and location data
                        const formData = new FormData();
                        formData.append('attendance_id', {{ $todayAttendance->id }});
                        formData.append('latitude', position.coords.latitude);
                        formData.append('longitude', position.coords.longitude);
                        formData.append('image', dataURLtoFile(img.src, 'check_out_photo.jpg'));
                        
                        // Send the data to the server
                        fetch('{{ route("attendance.check-out") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                status.innerHTML = '<span class="text-green-600">✓ Checked out successfully!</span>';
                                // Disable the check-out button
                                document.getElementById('check-out-btn').disabled = true;
                                document.getElementById('take-photo-out').disabled = true;
                            } else {
                                status.innerHTML = `<span class="text-red-600">✗ ${data.message}</span>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            status.innerHTML = '<span class="text-red-600">✗ An error occurred while checking out.</span>';
                        });
                        @else
                        status.innerHTML = '<span class="text-red-600">✗ No check-in record found for today.</span>';
                        @endif
                    },
                    function(error) {
                        status.innerHTML = '<span class="text-red-600">✗ Could not get location for check-out.</span>';
                    }
                );
            } else {
                status.innerHTML = '<span class="text-red-600">✗ Geolocation is not supported.</span>';
            }
        });
        
        // Helper function to convert data URL to File object
        function dataURLtoFile(dataurl, filename) {
            var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
            while(n--){
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new File([u8arr], filename, {type:mime});
        }
        
        // Validate location on page load
        window.onload = validateLocation;
    </script>
</x-app-layout>