@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Manage Appointment Slots</h2>
    <form method="POST" action="{{ route('admin.availability.store') }}" class="mb-4 row g-3">
        @csrf
        <div class="col-md-3">
            <select name="sacrament_type" class="form-select" required>
                <option value="">Select Sacrament</option>
                @foreach(['baptism','communion','confirmation','wedding','funeral'] as $type)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="available_date" class="form-control" required>
        </div>
        <div class="col-md-2">
            <input type="time" name="start_time" class="form-control" required>
        </div>
        <div class="col-md-2">
            <input type="time" name="end_time" class="form-control" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="max_slots" class="form-control" placeholder="Max slots" value="10">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary">Add</button>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Sacrament</th>
                <th>Date</th>
                <th>Time</th>
                <th>Slots</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($availabilities as $slot)
            <tr>
                <td>{{ ucfirst($slot->sacrament_type) }}</td>
                <td>{{ $slot->available_date->format('M d, Y') }}</td>
                <td>{{ $slot->start_time->format('H:i') }} - {{ $slot->end_time->format('H:i') }}</td>
                <td>{{ $slot->max_slots }}</td>
                <td>{{ $slot->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <form action="{{ route('admin.availability.toggle', $slot->id) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm {{ $slot->is_active ? 'btn-warning' : 'btn-success' }}">{{ $slot->is_active ? 'Deactivate' : 'Activate' }}</button>
                    </form>
                    <form action="{{ route('admin.availability.destroy', $slot->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete slot?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection