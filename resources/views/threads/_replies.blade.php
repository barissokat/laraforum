@forelse ($replies as $reply)
<reply :attributes="{{ $reply }}" inline-template>

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
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" name="" id="" rows="3" v-model="body"></textarea>
                </div>
                <button class="btn btn-outline-primary btn-sm" @click="update">Update</button>
                <button class="btn btn-link btn-sm" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body"> </div>
        </p>
        @can('update', $reply)
        <div class="card-footer d-flex">
            <button class="btn btn-secondary btn-sm mr-2" @click="editing = true">Edit</button>
            <form action="{{ route('replies.delete', $reply) }}" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    Delete
                </button>
            </form>
        </div>
        @endcan
    </div>

</reply>
@if(!$loop->last)
<hr>
@endif
@empty
<p>Reply not found!</p>
@endforelse
