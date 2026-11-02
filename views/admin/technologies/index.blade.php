@extends('admin.layouts.app')

@section('title', 'Technologies - Admin')

@push('styles')
<style nonce="{{ $cspNonce ?? '' }}">
    .admin-tech-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        flex-shrink: 0;
    }
    .admin-tech-icon img {
        width: 34px;
        height: 34px;
        object-fit: contain;
        display: block;
    }
    .admin-tech-icon--blue { background: rgba(0, 229, 255, 0.16); }
    .admin-tech-icon--purple,
    .admin-tech-icon--pink { background: rgba(224, 64, 251, 0.14); }
    .admin-tech-icon--purple-light { background: rgba(255, 183, 255, 0.28); }
    .admin-tech-icon--yellow { background: rgba(126, 249, 255, 0.22); }
    .admin-tech-icon--python { background: rgba(255, 218, 75, 0.28); }
    .admin-tech-icon--tableau { background: rgba(232, 118, 45, 0.16); }
    .admin-tech-initials {
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        color: #64748b;
    }
    .admin-tech-actions {
        display: inline-flex;
        gap: 0.35rem;
        justify-content: flex-end;
    }
    .admin-tech-actions .btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

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
                        <th scope="col" class="text-center" style="width: 88px;">Icon</th>
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
                            <td class="text-center">
                                <span class="admin-tech-icon admin-tech-icon--{{ $technology->color_class }}">
                                    @if($technology->icon_url)
                                        <img src="{{ $technology->icon_url }}"
                                             alt="{{ $technology->name }}"
                                             width="34"
                                             height="34">
                                    @else
                                        <span class="admin-tech-initials">{{ $technology->initials() }}</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $technology->name }}</div>
                                @if($technology->description)
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($technology->description, 60) }}</div>
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
                                <div class="admin-tech-actions">
                                    <a href="{{ route('admin.technologies.edit', $technology) }}" class="btn btn-sm btn-outline-primary" title="Edit {{ $technology->name }}" aria-label="Edit {{ $technology->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.technologies.destroy', $technology) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this technology?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete {{ $technology->name }}" aria-label="Delete {{ $technology->name }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
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
