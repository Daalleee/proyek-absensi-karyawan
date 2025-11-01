<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">My Profile Information</h1>
                        <a href="{{ route('profile.edit') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Profile
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold mb-4">Personal Information</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Employee Code</p>
                                    <p class="font-medium">{{ Auth::user()->employee->employee_code }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Full Name</p>
                                    <p class="font-medium">{{ Auth::user()->employee->first_name }} {{ Auth::user()->employee->last_name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium">{{ Auth::user()->employee->email }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Phone</p>
                                    <p class="font-medium">{{ Auth::user()->employee->phone ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold mb-4">Employment Information</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Position</p>
                                    <p class="font-medium">{{ Auth::user()->employee->position ?? '-' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Department</p>
                                    <p class="font-medium">{{ Auth::user()->employee->department ?? '-' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Hire Date</p>
                                    <p class="font-medium">{{ Auth::user()->employee->hire_date ? Auth::user()->employee->hire_date->format('M d, Y') : '-' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Role</p>
                                    <p class="font-medium">{{ ucfirst(Auth::user()->employee->role->name) }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ Auth::user()->employee->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           (Auth::user()->employee->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-red-100 text-red-800') }}">
                                        {{ ucfirst(Auth::user()->employee->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold mb-4">Attendance Statistics</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-semibold mb-2">Total Attendances</h3>
                                <p class="text-2xl font-bold text-blue-600">{{ Auth::user()->employee->attendances->count() }}</p>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-semibold mb-2">Valid Locations</h3>
                                <p class="text-2xl font-bold text-green-600">{{ Auth::user()->employee->attendances->where('is_check_in_valid', true)->count() }}</p>
                            </div>
                            
                            <div class="bg-yellow-50 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-semibold mb-2">Face Matched</h3>
                                <p class="text-2xl font-bold text-yellow-600">{{ Auth::user()->employee->attendances->where('is_face_recognized', true)->count() }}</p>
                            </div>
                            
                            <div class="bg-purple-50 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-semibold mb-2">Completed</h3>
                                <p class="text-2xl font-bold text-purple-600">{{ Auth::user()->employee->attendances->where('status', 'completed')->count() }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <a href="{{ route('attendance.history') }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                View Full Attendance History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>