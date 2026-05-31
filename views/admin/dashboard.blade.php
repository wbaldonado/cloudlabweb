@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Blogs</h5>
                <h2 class="display-4">{{ $blogCount }}</h2>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-primary">Manage Blogs</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Projects</h5>
                <h2 class="display-4">{{ $projectCount }}</h2>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-primary">Manage Projects</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Technologies</h5>
                <h2 class="display-4">{{ $technologyCount }}</h2>
                <a href="{{ route('admin.technologies.index') }}" class="btn btn-primary">Manage Technologies</a>
            </div>
        </div>
    </div>
</div>
@endsection

