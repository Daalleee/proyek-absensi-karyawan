<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendance Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Attendance Details</h1>
                        <a href="{{ route('admin.attendances.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to List
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold mb-4">Employee Information</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Name</p>
                                    <p class="font-medium">{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Employee Code</p>
                                    <p class="font-medium">{{ $attendance->employee->employee_code }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium">{{ $attendance->employee->email }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Position</p>
                                    <p class="font-medium">{{ $attendance->employee->position ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold mb-4">Work Location</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Location Name</p>
                                    <p class="font-medium">{{ $attendance->workLocation->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Date</p>
                                    <p class="font-medium">{{ \Carbon\Carbon::parse($attendance->workLocation->date)->format('M d, Y') }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Coordinates</p>
                                    <p class="font-medium">{{ $attendance->workLocation->latitude }}, {{ $attendance->workLocation->longitude }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Radius</p>
                                    <p class="font-medium">{{ $attendance->workLocation->radius }} meters</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold mb-4">Check In Details</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Time</p>
                                    <p class="font-medium">{{ $attendance->check_in_time ? $attendance->check_in_time->format('M d, Y H:i:s') : '-' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Coordinates</p>
                                    <p class="font-medium">{{ $attendance->check_in_latitude ? $attendance->check_in_latitude . ', ' . $attendance->check_in_longitude : '-' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Location Valid</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $attendance->is_check_in_valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $attendance->is_check_in_valid ? 'Valid' : 'Invalid' }}
                                    </span>
                                </div>
                                
                                @if($attendance->check_in_image_path)
                                <div>
                                    <p class="text-sm text-gray-500">Check In Photo</p>
                                    <img src="{{ Storage::url($attendance->check_in_image_path) }}" alt="Check In Photo" class="mt-2 max-w-xs rounded">
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold mb-4">Check Out Details</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Time</p>
                                    <p class="font-medium">{{ $attendance->check_out_time ? $attendance->check_out_time->format('M d, Y H:i:s') : '-' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Coordinates</p>
                                    <p class="font-medium">{{ $attendance->check_out_latitude ? $attendance->check_out_latitude . ', ' . $attendance->check_out_longitude : '-' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Location Valid</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $attendance->is_check_out_valid ? 'bg-green-100 text-green-800' : ($attendance->check_out_time ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $attendance->check_out_time ? ($attendance->is_check_out_valid ? 'Valid' : 'Invalid') : 'N/A' }}
                                    </span>
                                </div>
                                
                                @if($attendance->check_out_image_path)
                                <div>
                                    <p class="text-sm text-gray-500">Check Out Photo</p>
                                    <img src="{{ Storage::url($attendance->check_out_image_path) }}" alt="Check Out Photo" class="mt-2 max-w-xs rounded">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 p-6 rounded-lg mb-8">
                        <h2 class="text-lg font-semibold mb-4">Verification Status</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Face Recognition</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $attendance->is_face_recognized ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mt-1">
                                    {{ $attendance->is_face_recognized ? 'Recognized' : 'Not Recognized' }}
                                </span>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Attendance Status</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($attendance->status === 'approved') bg-green-100 text-green-800
                                    @elseif($attendance->status === 'rejected') bg-red-100 text-red-800
                                    @elseif($attendance->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif mt-1">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Total Duration</p>
                                <p class="font-medium mt-1">
                                    @if($attendance->check_in_time && $attendance->check_out_time)
                                        {{ $attendance->check_in_time->diffInHours($attendance->check_out_time) }} hours
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if($attendance->notes)
                    <div class="bg-yellow-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold mb-4">Notes</h2>
                        <p class="text-gray-700">{{ $attendance->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>