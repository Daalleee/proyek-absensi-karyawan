<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendances') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Attendance Records</h1>
                    </div>

                    <!-- Filters and Export -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="employee_filter" class="block text-sm font-medium text-gray-700">Employee</label>
                            <select id="employee_filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Employees</option>
                                <!-- Employee options would be populated here -->
                            </select>
                        </div>
                        
                        <div>
                            <label for="date_filter" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" id="date_filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label for="status_filter" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status_filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="export_format" class="block text-sm font-medium text-gray-700">Export</label>
                            <div class="flex space-x-2 mt-1">
                                <button id="export_excel" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-3 rounded text-sm">
                                    Excel
                                </button>
                                <button id="export_pdf" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-3 rounded text-sm">
                                    PDF
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-end">
                            <button id="apply_filters" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                    
                    <!-- Report Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Total Attendances</h3>
                            <p class="text-2xl font-bold text-blue-600">{{ $attendances->total() }}</p>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Valid Locations</h3>
                            <p class="text-2xl font-bold text-green-600">{{ $attendances->getCollection()->where('is_check_in_valid', true)->count() }}</p>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Face Matched</h3>
                            <p class="text-2xl font-bold text-yellow-600">{{ $attendances->getCollection()->where('is_face_recognized', true)->count() }}</p>
                        </div>
                        
                        <div class="bg-purple-50 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Completed</h3>
                            <p class="text-2xl font-bold text-purple-600">{{ $attendances->getCollection()->where('status', 'completed')->count() }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location Valid</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Face Recognized</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($attendances as $attendance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $attendance->employee->employee_code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->workLocation->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : '-' }}</div>
                                        <div class="text-sm text-gray-500">{{ $attendance->check_in_latitude ? $attendance->check_in_latitude . ', ' . $attendance->check_in_longitude : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : '-' }}</div>
                                        <div class="text-sm text-gray-500">{{ $attendance->check_out_latitude ? $attendance->check_out_latitude . ', ' . $attendance->check_out_longitude : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $attendance->is_check_in_valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $attendance->is_check_in_valid ? 'Valid' : 'Invalid' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $attendance->is_face_recognized ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $attendance->is_face_recognized ? 'Recognized' : 'Not Recognized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($attendance->status === 'approved') bg-green-100 text-green-800
                                            @elseif($attendance->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($attendance->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.attendances.show', $attendance->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const exportExcelBtn = document.getElementById('export_excel');
            const exportPdfBtn = document.getElementById('export_pdf');
            const employeeFilter = document.getElementById('employee_filter');
            const dateFilter = document.getElementById('date_filter');
            const statusFilter = document.getElementById('status_filter');
            const applyFiltersBtn = document.getElementById('apply_filters');
            
            // Export to Excel
            exportExcelBtn.addEventListener('click', function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.attendances.export-excel") }}';
                
                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]') || document.querySelector('input[name="_token"]');
                const tokenValue = csrfToken ? csrfToken.getAttribute('content') || csrfToken.value : '';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = tokenValue;
                form.appendChild(tokenInput);
                
                // Add filter values
                addFilterInput(form, 'from_date', dateFilter.value || '{{ request()->date ?? today()->toDateString() }}');
                addFilterInput(form, 'to_date', dateFilter.value || '{{ today()->toDateString() }}');
                addFilterInput(form, 'employee_id', employeeFilter.value);
                
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            });
            
            // Export to PDF
            exportPdfBtn.addEventListener('click', function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.attendances.export-pdf") }}';
                
                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]') || document.querySelector('input[name="_token"]');
                const tokenValue = csrfToken ? csrfToken.getAttribute('content') || csrfToken.value : '';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = tokenValue;
                form.appendChild(tokenInput);
                
                // Add filter values
                addFilterInput(form, 'from_date', dateFilter.value || '{{ request()->date ?? today()->toDateString() }}');
                addFilterInput(form, 'to_date', dateFilter.value || '{{ today()->toDateString() }}');
                addFilterInput(form, 'employee_id', employeeFilter.value);
                
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            });
            
            // Helper function to add filter inputs
            function addFilterInput(form, name, value) {
                if (value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    form.appendChild(input);
                }
            }
        });
    </script>
</x-app-layout>