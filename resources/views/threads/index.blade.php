@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">Forum Threads</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @foreach ($threads as $thread)
                    <article>
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
                        <div class="card-text">
                            {{ $thread->body }}
                        </div>
                    </article>
                    @if(!$loop->last)
                    <hr>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
