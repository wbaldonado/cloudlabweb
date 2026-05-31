@extends('admin.layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Knowledge Base - Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h1 class="h3 mb-0">Knowledge Base</h1>
    <a href="{{ route('admin.knowledge-base.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Question & Answer
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 40%;">Question (ES / EN)</th>
                        <th scope="col" style="width: 40%;">Answer Preview</th>
                        <th scope="col" style="width: 80px;">Order</th>
                        <th scope="col" style="width: 100px;">Status</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($knowledgeBase as $entry)
                        <tr>
                            <td>
                                <div class="fw-semibold mb-1">
                                    <small class="text-muted">ES:</small> {{ $entry->question_es ? Str::limit($entry->question_es, 50) : '—' }}
                                </div>
                                <div class="text-muted small">
                                    <small class="text-muted">EN:</small> {{ $entry->question_en ? Str::limit($entry->question_en, 50) : '—' }}
                                </div>
                            </td>
                            <td>
                                <div class="small">{{ Str::limit($entry->answer_es, 80) }}</div>
                            </td>
                            <td>{{ $entry->display_order }}</td>
                            <td>
                                @if($entry->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.knowledge-base.edit', $entry) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.knowledge-base.destroy', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this entry?')">
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
                            <td colspan="5" class="text-center py-4 text-muted">No knowledge base entries found. Start by adding one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($knowledgeBase->hasPages())
        <div class="card-footer">
            {{ $knowledgeBase->links() }}
        </div>
    @endif
</div>
@endsection

