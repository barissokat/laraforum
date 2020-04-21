@forelse ($replies as $reply)
<reply :attributes="{{ $reply }}" inline-template>

    <div id="reply-{{ $reply->id }}" class="card">
        <div class="card-body">
            <h5 class="card-title d-flex justify-content-between">
                <div class="flex-grow-1 align-self-center">
                    <a href="{{ route('profiles.show', $reply->owner->name ) }}">{{ $reply->owner->name }}</a> said
                    {{ $reply->created_at->diffForHumans() }}
                </div>
                <div class="flex-shrink-1">
                    <favorite :reply="{{ $reply }}"></favorite>
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
                <button class="btn btn-danger btn-sm mr-2" @click="destroy">Delete</button>
            </div>
            @endcan
        </div>
    </div>
</reply>
<hr>
@empty
<p>Reply not found!</p>
@endforelse
