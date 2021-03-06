@forelse ($threads as $thread)
<div class="card">
    <div class="card-header d-flex">
        @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
        @endif
        <div class="d-flex flex-column flex-fill">
            <h4 class="card-title">
                @if($thread->pinned)
                <span class="fas fa-thumbtack" aria-hidden="true"></span>
                @endif
                <a href="{{ $thread->path() }}">
                    @if (auth()->user() && $thread->hasUpdatesFor(auth()->user()))
                    <strong>
                        {{ $thread->title }}
                    </strong>
                    @else
                    {{ $thread->title }}
                    @endif
                </a>
            </h4>

            <h5>
                Posted By:
                <a href="{{ route('profiles.show', $thread->owner->username) }}">
                    {{ $thread->owner->username }}
                </a>
            </h5>
        </div>

        <a href="{{ $thread->path() }}">
            <strong>
                {{ $thread->replies_count }}
                {{ Str::plural('reply', $thread->replies_count) }}
            </strong>
        </a>
    </div>

    <div class="card-body">
        <div class="card-text">
            <thread-view :thread="{{ $thread }}" inline-template>
                <highlight :content="body"></highlight>
            </thread-view>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <span class="badge badge-secondary">{{ $thread->visits }} Visits</span>
        <span class="badge badge-primary">
            <a class="text-white" href="/threads/{{ $thread->channel->slug }}">
                {{ $thread->channel->name}}
            </a>
        </span>
    </div>
</div>
@if(!$loop->last)
<hr>
@endif
@empty
<p>There are no relevant results at this time.</p>
@endforelse
