@extends('layouts.admin')

@section('content')
<div class="container">

    <h2 class="mb-4">Manage Appointment Slots</h2>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ============================================================ --}}
    {{-- Add Availability Form (inline calendar)                       --}}
    {{-- ============================================================ --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Add Availability for a Date</h5>
            <p class="text-muted small mb-3">
                The following 4 time slots will be added automatically:
                <strong>8:00 AM – 9:00 AM</strong>,
                <strong>10:00 AM – 11:00 AM</strong>,
                <strong>1:00 PM – 2:00 PM</strong>,
                <strong>3:00 PM – 4:00 PM</strong>.
                If the date already has slots, they will be skipped.
            </p>

            <form method="POST" action="{{ route('admin.availability.bulk') }}">
                @csrf

                <div class="row">
                    {{-- Inline Calendar --}}
                    <div class="col-md-6">
                        <label class="form-label">Pick a Date</label>
                        <div id="admin-calendar"></div>
                        <input type="hidden" name="available_date" id="available_date" required>
                        <p class="text-muted small mt-2 mb-0">
                            Selected: <strong id="selected-date-label">None</strong>
                        </p>
                    </div>

                    {{-- Submit Button --}}
                    <div class="col-md-6 d-flex align-items-center">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Add All Slots for Selected Date
                        </button>
                    </div>
                </div>
            </form>

            @if($errors->any())
                <div class="alert alert-danger mt-3 mb-0">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Active Availability (today + future)                          --}}
    {{-- ============================================================ --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <span class="badge bg-success me-2">Active</span>
                Current &amp; Upcoming Availability
            </h5>

            @if($activeAvailabilities->isEmpty())
                <p class="text-muted mb-0">No active availability records. Add a date above to get started.</p>
            @else
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time Slot</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeAvailabilities as $slot)
                            @php
                                // Safe date formatting (handles Carbon + string)
                                $dateStr = $slot->available_date instanceof \DateTimeInterface
                                    ? $slot->available_date->format('M d, Y')
                                    : \Carbon\Carbon::parse($slot->available_date)->format('M d, Y');

                                $start = $slot->start_time instanceof \DateTimeInterface
                                    ? \Carbon\Carbon::parse($slot->start_time)->format('g:i A')
                                    : \Carbon\Carbon::parse(substr($slot->start_time, 0, 5))->format('g:i A');

                                $end = $slot->end_time instanceof \DateTimeInterface
                                    ? \Carbon\Carbon::parse($slot->end_time)->format('g:i A')
                                    : \Carbon\Carbon::parse(substr($slot->end_time, 0, 5))->format('g:i A');
                            @endphp
                            <tr>
                                <td>{{ $dateStr }}</td>
                                <td>{{ $start }} – {{ $end }}</td>
                                <td>
                                    @if($slot->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.availability.toggle', $slot->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $slot->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $slot->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.availability.destroy', $slot->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this slot?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Archive (past dates)                                          --}}
    {{-- ============================================================ --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <span class="badge bg-secondary me-2">Archive</span>
                Past Availability
            </h5>

            @if($archivedAvailabilities->isEmpty())
                <p class="text-muted mb-0">No archived availability. Past dates will appear here automatically.</p>
            @else
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time Slot</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivedAvailabilities as $slot)
                            @php
                                $dateStr = $slot->available_date instanceof \DateTimeInterface
                                    ? $slot->available_date->format('M d, Y')
                                    : \Carbon\Carbon::parse($slot->available_date)->format('M d, Y');

                                $start = $slot->start_time instanceof \DateTimeInterface
                                    ? \Carbon\Carbon::parse($slot->start_time)->format('g:i A')
                                    : \Carbon\Carbon::parse(substr($slot->start_time, 0, 5))->format('g:i A');

                                $end = $slot->end_time instanceof \DateTimeInterface
                                    ? \Carbon\Carbon::parse($slot->end_time)->format('g:i A')
                                    : \Carbon\Carbon::parse(substr($slot->end_time, 0, 5))->format('g:i A');
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $dateStr }}</td>
                                <td class="text-muted">{{ $start }} – {{ $end }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.availability.destroy', $slot->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this archived slot permanently?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection

{{-- ============================================================ --}}
{{-- Flatpickr Inline Calendar                                     --}}
{{-- ============================================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr === 'undefined') {
        console.warn('Flatpickr not loaded');
        return;
    }

    flatpickr("#admin-calendar", {
        inline: true,
        dateFormat: "Y-m-d",
        minDate: "today",
        theme: "dark",
        defaultDate: null,
        onChange: function(selectedDates, dateStr, instance) {
            document.getElementById('available_date').value = dateStr;

            if (dateStr) {
                const date = new Date(dateStr + 'T00:00:00');
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('selected-date-label').textContent =
                    date.toLocaleDateString('en-US', options);
            } else {
                document.getElementById('selected-date-label').textContent = 'None';
            }
        }
    });
});
</script>
@endpush