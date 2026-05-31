@extends('admin.layouts.app')

@section('title', 'Projects - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Projects</h1>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create New Project
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Featured</th>
                <th>Published</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
            <tr>
                <td>{{ $project->id }}</td>
                <td>{{ $project->title }}</td>
                <td>{{ $project->user->name ?? 'N/A' }}</td>
                <td>
                    @if($project->is_featured)
                    <span class="badge bg-warning">Featured</span>
                    @else
                    <span class="badge bg-secondary">No</span>
                    @endif
                </td>
                <td>
                    @if($project->is_published)
                    <span class="badge bg-success">Published</span>
                    @else
                    <span class="badge bg-secondary">Draft</span>
                    @endif
                </td>
                <td>{{ $project->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No projects found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {{ $projects->links() }}
</div>
@endsection

