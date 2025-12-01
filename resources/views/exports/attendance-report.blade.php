<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: bold;">LAPORAN ABSENSI KARYAWAN</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: bold;">
                Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}
                @if($employeeName)
                    | Karyawan: {{ $employeeName }}
                @endif
            </th>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama Karyawan</th>
            <th>ID Karyawan</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $index => $attendance)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $attendance->employee->user->name ?? 'N/A' }}</td>
            <td>{{ $attendance->employee->employee_id ?? 'N/A' }}</td>
            <td>{{ $attendance->date->format('d/m/Y') }}</td>
            <td>{{ $attendance->clock_in ?? '-' }}</td>
            <td>{{ $attendance->clock_out ?? '-' }}</td>
            <td>{{ $attendance->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>