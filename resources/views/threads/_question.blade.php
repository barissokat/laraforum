{{-- Editing the question --}}
<div class="card" v-if="editing">
    <div class="card-header">
        <div class="form-group mb-0">
            <input type="text" class="form-control" v-model="form.title">
        </div>
    </div>
    <div class="card-body">
        <div class="form-group">
            <wysiwyg v-model="form.body" :value="form.body"></wysiwyg>
        </div>
    </div>
    <div class="card-footer d-flex">
        <button class="btn btn-primary btn-sm mr-2" @click="update">Update</button>
        <button class="btn btn-secondary btn-sm" @click="resetForm">Cancel</button>

        @can('update', $thread)
        <form action="{{ $thread->path() }}" method="post" class="ml-auto">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-link">Delete Thread</button>
        </form>
        @endcan
    </div>
</div>

{{-- Viewing the question --}}
<div class="card" v-else>
    <div class="card-header">
        <div class="d-flex align-items-center mb-2">
            <img src="{{ $thread->owner->avatar }}" class="rounded-circle mr-2" width="25" height="25"
                alt="{{ $thread->owner->name }}">

            <h4 class="card-title d-inline mb-0"><a
                    href="{{ route('profiles.show', $thread->owner->name ) }}">{{ $thread->owner->name }}</a>
                posted: <span v-text="title"></span>
            </h4>
        </div>
    </div>
    <div class="card-body">
        <p class="card-text" v-html="body">
        </p>
        <a href="{{ route('threads.index') }}" class="btn-link">Back</a>
    </div>
    <div class="card-footer" v-if="authorize('owns', thread)">
        <button class="btn btn-primary btn-sm" @click="editing = true">Edit</button>
    </div>
</div>
