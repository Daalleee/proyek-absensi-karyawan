<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Welcome, {{ Auth::user()->employee->first_name }}!</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Your Status</h3>
                            <p class="text-lg font-bold text-blue-600">{{ ucfirst(Auth::user()->employee->status) }}</p>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Total Attendances</h3>
                            <p class="text-lg font-bold text-green-600">{{ Auth::user()->employee->attendances()->count() }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ route('employee.attendance.index') }}" class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 transition">
                                <div class="text-blue-500 text-2xl mb-2">✅</div>
                                <h3 class="font-semibold">Attendance Check</h3>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 transition">
                                <div class="text-yellow-500 text-2xl mb-2">⚙️</div>
                                <h3 class="font-semibold">Profile Settings</h3>
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4">Today's Work Location</h2>
                        @php
                            $todayWorkLocation = \App\Models\WorkLocation::where('date', today())->first();
                        @endphp
                        
                        @if($todayWorkLocation)
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <h3 class="font-semibold">{{ $todayWorkLocation->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $todayWorkLocation->description }}</p>
                                <p class="text-sm mt-1">Coordinates: {{ $todayWorkLocation->latitude }}, {{ $todayWorkLocation->longitude }}</p>
                                <p class="text-sm">Radius: {{ $todayWorkLocation->radius }} meters</p>
                            </div>
                        @else
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <p class="text-yellow-800">No work location assigned for today. Please contact your supervisor.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>