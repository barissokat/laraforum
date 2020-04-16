@extends('layouts.app')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-3">Forum Threads</h1>
    </div>
</div>
<div class="container">
    @foreach ($threads as $thread)
    <div class="card">
        <div class="card-header">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif
            <div class="d-flex">
                <h4 class="card-title flex-fill">
                    <a href="{{ $thread->path() }}">{{ $thread->title }}</a>
                </h4>
                <a href="{{ $thread->path() }}">
                    <strong>
                        {{ $thread->replies_count }}
                        {{ Str::plural('reply', $thread->replies_count) }}
                    </strong>
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="card-text">
                {{ $thread->body }}
            </div>
        </div>
    </div>
    @if(!$loop->last)
    <hr>
    @endif
    @endforeach
</div>
@endsection
