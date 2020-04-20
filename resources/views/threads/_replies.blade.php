@forelse ($replies as $reply)
<div id="reply-{{ $reply->id }}">
    <h5 class="card-title d-flex justify-content-between">
        <div class="flex-grow-1 align-self-center">
            <a href="{{ route('profiles.show', $reply->owner->name ) }}">{{ $reply->owner->name }}</a> said
            {{ $reply->created_at->diffForHumans() }}
        </div>
        <div class="flex-shrink-1">
            <form action="{{ route('replies.favorite', $reply) }}" method="post">
                @csrf
                <button type="submit" class="btn btn-primary" {{ $reply->isFavorited() ? 'disabled' : '' }}>
                    {{ $reply->favorites_count }} {{ Str::plural('Favorite', $reply->favorites_count) }}
                </button>
            </form>
        </div>
    </h5>
    <p class="card-text">
        {{ $reply->body }}
    </p>
</div>
@if(!$loop->last)
<hr>
@endif
@empty
<p>Reply not found!</p>
@endforelse
