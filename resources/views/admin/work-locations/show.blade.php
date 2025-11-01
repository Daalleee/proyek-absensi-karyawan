<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Work Location Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Work Location Details</h1>
                        <div>
                            <a href="{{ route('admin.work-locations.edit', $workLocation->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Edit
                            </a>
                            <a href="{{ route('admin.work-locations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to List
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold mb-4">Location Information</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Name</p>
                                    <p class="font-medium">{{ $workLocation->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Description</p>
                                    <p class="font-medium">{{ $workLocation->description ?? '-' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Date</p>
                                    <p class="font-medium">{{ \Carbon\Carbon::parse($workLocation->date)->format('M d, Y') }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $workLocation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($workLocation->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold mb-4">Geographic Information</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Latitude</p>
                                    <p class="font-medium">{{ $workLocation->latitude }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Longitude</p>
                                    <p class="font-medium">{{ $workLocation->longitude }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Radius</p>
                                    <p class="font-medium">{{ $workLocation->radius }} meters</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Created By</p>
                                    <p class="font-medium">{{ $workLocation->creator->first_name }} {{ $workLocation->creator->last_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($workLocation->start_time || $workLocation->end_time)
                    <div class="bg-blue-50 p-6 rounded-lg mb-8">
                        <h2 class="text-lg font-semibold mb-4">Working Hours</h2>
                        
                        <div class="flex space-x-4">
                            @if($workLocation->start_time)
                            <div>
                                <p class="text-sm text-gray-500">Start Time</p>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($workLocation->start_time)->format('H:i') }}</p>
                            </div>
                            @endif
                            
                            @if($workLocation->end_time)
                            <div>
                                <p class="text-sm text-gray-500">End Time</p>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($workLocation->end_time)->format('H:i') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold mb-4">Location on Map</h2>
                        <div id="map" class="h-64 w-full rounded-lg border border-gray-300" style="min-height: 300px;">
                            <div class="flex items-center justify-center h-full bg-gray-100 rounded">
                                <p class="text-gray-500">Interactive map would show the location and geofence area</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold mb-4">Associated Attendances</h2>
                        
                        @if($workLocation->attendances->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In Time</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out Time</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location Valid</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($workLocation->attendances->take(10) as $attendance)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($attendance->is_check_in_valid)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Valid</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Invalid</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">No attendance records for this location.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Map functionality (placeholder)
        document.addEventListener('DOMContentLoaded', function() {
            // In a real implementation, this would initialize an actual map
            const mapContainer = document.getElementById('map');
            mapContainer.innerHTML = `
                <div class="p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Location: {{ $workLocation->name }}</h3>
                        <span class="text-sm">Lat: {{ $workLocation->latitude }}, Lng: {{ $workLocation->longitude }}</span>
                    </div>
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl h-48 flex items-center justify-center">
                        <p>Map showing location and a circle representing the {{ $workLocation->radius }}m geofence radius</p>
                    </div>
                    <div class="mt-3 text-sm text-gray-600">
                        <p>Geofence area: {{ $workLocation->radius }} meters radius</p>
                        <p>Center coordinates: {{ $workLocation->latitude }}, {{ $workLocation->longitude }}</p>
                    </div>
                </div>
            `;
        });
    </script>
</x-app-layout>