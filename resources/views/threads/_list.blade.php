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
                <a href="{{ route('profiles.show', $thread->owner->name) }}">
                    {{ $thread->owner->name }}
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
            {!! $thread->body !!}
        </div>
    </div>
    <div class="card-footer">
        <span class="badge badge-primary">{{ $thread->visits }} Visits</span>
    </div>
</div>
@if(!$loop->last)
<hr>
@endif
@empty
<p>There are no relevant results at this time.</p>
@endforelse
