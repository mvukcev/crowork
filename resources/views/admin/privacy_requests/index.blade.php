@extends('layouts.admin')

@section('content')
    <h1>Privacy Requests</h1>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ $request->status }}</td>
                    <td>
                        <form action="{{ route('admin.privacy_requests.update', $request) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status">
                                <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $request->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $request->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection