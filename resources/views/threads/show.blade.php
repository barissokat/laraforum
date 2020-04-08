@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><a href="#">{{ $thread->owner->name }}</a> posted: {{ $thread->title }}</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        {{ $thread->body }}
                    </p>
                    <a href="{{ route('threads.index') }}" class="btn-link">Back</a>
                </div>
            </div>
            <hr>
            <h2>Replies</h2>
            <div class="card">
                <div class="card-body">
                    @include('threads._replies')
                </div>

                <div class="d-flex justify-content-center">
                    {{ $replies->links() }}
                </div>


                <div class="card-footer text-muted">
                    @auth
                    <form action="{{ $thread->path() . '/replies' }}" method="post">
                        @csrf
                        <div class="form-group">
                            <textarea class="form-control" name="body" id="body" rows="3"
                                placeholder="Have something to say?"></textarea>
                            @error('body')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                    @else
                    <p class="text-muted text-center">
                        Please <a href="{{ route('login') }}">sign in</a> to participate in this discussion.
                    </p>
                    @endauth
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    This thread was published <strong>{{ $thread->created_at->diffForHumans() }}</strong>
                    by <a href="#">{{ $thread->owner->name }}</a>
                    , and currently has <strong>{{ $thread->replies_count }}</strong>
                    {{ Str::plural('comment', $thread->replies_count) }}.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
