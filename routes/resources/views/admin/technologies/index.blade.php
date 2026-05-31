@extends('admin.layouts.app')

@php 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Technologies - Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h1 class="h3 mb-0">Technologies</h1>
    <a href="{{ route('admin.technologies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Technology
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 80px;">Icon</th>
                        <th scope="col">Name</th>
                        <th scope="col">Order</th>
                        <th scope="col">Color Style</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($technologies as $technology)
                        <tr>
                            <td>
                                @if($technology->icon_path)
                                    @php
                                        $iconExists = Storage::disk('public')->exists($technology->icon_path);
                                        $iconUrl = $iconExists ? asset('storage/' . $technology->icon_path) : null;
                                    @endphp
                                    @if($iconUrl)
                                        <img src="{{ $iconUrl }}?v={{ time() }}" 
                                             alt="{{ $technology->name }}" 
                                             class="img-fluid" 
                                             style="max-width: 48px; max-height: 48px; height: auto; object-fit: contain;"
                                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'48\'%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3E?%3C/text%3E%3C/svg%3E';">
                                    @else
                                        <span class="text-muted small" title="File: {{ $technology->icon_path }}">Not found</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $technology->name }}</div>
                                @if($technology->description)
                                    <div class="text-muted small">{{ Str::limit($technology->description, 60) }}</div>
                                @endif
                            </td>
                            <td>{{ $technology->display_order }}</td>
                            <td><span class="badge bg-secondary text-uppercase">{{ $technology->color_class }}</span></td>
                            <td>
                                @if($technology->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.technologies.edit', $technology) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.technologies.destroy', $technology) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this technology?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No technologies found. Start by adding one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
