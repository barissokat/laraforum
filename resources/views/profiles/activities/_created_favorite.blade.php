@component('profiles.activities.activity')
@slot('heading')
<span class="d-flex align-items-center">
    <i class="fas fa-heart fa-2x mr-2"></i>
    <span class="mr-1">
        {{ $profileUser->name }} favorited a
    </span>
    <a href="{{ $activity->subject->favorited->path() }}">
        reply
    </a>.
</span>
<span class="d-flex align-items-center">
    {{ $activity->created_at->diffForHumans() }}
</span>
@endslot
@slot('body')
<div class="card-text">
    {!! $activity->subject->favorited->body !!}
</div>
@endslot
@endcomponent
