<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Total Employees</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Employee::count() }}</p>
                        </div>

                        <div class="bg-green-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Active Work Locations</h3>
                            <p class="text-3xl font-bold text-green-600">
                                {{ \App\Models\WorkLocation::where('status', 'active')->count() }}</p>
                        </div>

                        <div class="bg-purple-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Today's Attendances</h3>
                            <p class="text-3xl font-bold text-purple-600">
                                {{ \App\Models\Attendance::whereDate('created_at', today())->count() }}</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ route('admin.employees.index') }}"
                                class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 transition">
                                <div class="text-blue-500 text-2xl mb-2">👥</div>
                                <h3 class="font-semibold">Manage Employees</h3>
                            </a>
                            <a href="{{ route('admin.work-locations.index') }}"
                                class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 transition">
                                <div class="text-green-500 text-2xl mb-2">📍</div>
                                <h3 class="font-semibold">Manage Work Locations</h3>
                            </a>
                            <a href="{{ route('admin.attendances.index') }}"
                                class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 transition">
                                <div class="text-purple-500 text-2xl mb-2">📋</div>
                                <h3 class="font-semibold">View Attendances</h3>
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 transition">
                                <div class="text-yellow-500 text-2xl mb-2">👤</div>
                                <h3 class="font-semibold">Profile Settings</h3>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
