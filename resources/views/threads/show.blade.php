@extends('layouts.app')

@section('head')
<link href="{{ asset('css/vendor/tribute.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-3">{{ $thread->title }}</h1>
    </div>
</div>

<thread-view :initial-replies-count="{{ $thread->replies_count }}" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="flex">
                            <h4 class="card-title"><a
                                    href="{{ route('profiles.show', $thread->owner->name ) }}">{{ $thread->owner->name }}</a>
                                posted: {{ $thread->title }}</h4>
                        </div>
                        @can('update', $thread)
                        <form action="{{ $thread->path() }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-primary">Delete Thread</button>
                        </form>
                        @endcan
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
                <replies @added="repliesCount++" @removed="repliesCount--"></replies>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        This thread was published <strong>{{ $thread->created_at->diffForHumans() }}</strong>
                        by <a href="{{ route('profiles.show', $thread->owner->name ) }}">{{ $thread->owner->name }}</a>
                        , and currently has
                        <span v-text="repliesCount"></span>
                        {{ Str::plural('reply', $thread->replies_count) }}.
                        <hr>
                        <div>
                            <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}"></subscribe-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</thread-view>
@endsection
