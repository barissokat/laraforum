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

<thread-view :thread="{{ $thread }}" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                @include('threads._question')

                <hr>

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

                        <div>
                            <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}" v-if="signedIn">
                            </subscribe-button>

                            <button class="btn btn-warning btn-block"
                                v-if="authorize('isAdmin')" @click="toggleLock"
                                v-text="locked ? 'Unlock' : 'Lock'"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</thread-view>
@endsection
