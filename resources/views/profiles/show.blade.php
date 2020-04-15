@extends('layouts.app')

@section('content')
<div class="container">
    <div class="jumbotron">
        <h1 class="display-3">
            {{ $profileUser->name }}</h1>
        <p class="lead">
            <small>Since {{ $profileUser->created_at->diffForHumans() }}</small>
        </p>
    </div>

    @foreach ($threads as $thread)
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">{{ $thread->title }}</h4>
            <small>Since {{ $thread->created_at->diffForHumans() }}</small>
        </div>
        <div class="card-body">
            <p class="card-text">
                {{ $thread->body }}
            </p>
        </div>
    </div>
    @endforeach

    {{ $threads->links() }}
</div>
@endsection
