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

    {{-- Add Availability Form --}}
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

            <form method="POST" action="{{ route('admin.availability.bulk') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label for="available_date" class="form-label">Date</label>
                    <input
                        type="date"
                        name="available_date"
                        id="available_date"
                        class="form-control"
                        required
                        min="{{ now()->toDateString() }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Add All Slots</button>
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

    {{-- Existing Availability Table --}}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Existing Availability</h5>

            @if($availabilities->isEmpty())
                <p class="text-muted">No availability records yet. Add a date above to get started.</p>
            @else
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time Slot</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($availabilities as $slot)
                            @php
                                // Format start_time (handles both Carbon and string)
                                if ($slot->start_time instanceof \DateTimeInterface) {
                                    $start = \Carbon\Carbon::parse($slot->start_time)->format('g:i A');
                                } else {
                                    $start = \Carbon\Carbon::parse(substr($slot->start_time, 0, 5))->format('g:i A');
                                }

                                if ($slot->end_time instanceof \DateTimeInterface) {
                                    $end = \Carbon\Carbon::parse($slot->end_time)->format('g:i A');
                                } else {
                                    $end = \Carbon\Carbon::parse(substr($slot->end_time, 0, 5))->format('g:i A');
                                }

                                if ($slot->available_date instanceof \DateTimeInterface) {
                                    $dateStr = $slot->available_date->format('M d, Y');
                                } else {
                                    $dateStr = \Carbon\Carbon::parse($slot->available_date)->format('M d, Y');
                                }
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
</div>
@endsection