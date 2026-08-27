@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Daphnia Magna Bioassays</h2>

        <a href="{{ route('daphnia-magna.create') }}" class="btn btn-success mb-3">+ New Bioassay</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sample</th>
                    <th>Analyst</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bioassays as $bioassay)
                    <tr>
                        <td>{{ $bioassay->id }}</td>
                        <td>{{ $bioassay->sample }}</td>
                        <td>{{ $bioassay->analyst }}</td>
                        <td>{{ $bioassay->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ url('daphnia_magna.edit', $bioassay) }}"
                                class="btn btn-primary btn-sm">Edit</a>

                            <form action="{{ url('daphnia_magna.destroy', $bioassay) }}" method="POST"
                                style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No bioassays found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $bioassays->links() }}
    </div>
@endsection
