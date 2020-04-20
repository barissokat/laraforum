@component('profiles.activities.activity')
@slot('heading')
<span class="d-flex align-items-center">
    <i class="fas fa-feather-alt fa-2x mr-2"></i>
    <span class="mr-1">
        {{ $profileUser->name }} published
    </span>
    <a href="{{$activity->subject->path() }}">
        "{{ $activity->subject->title }}"
    </a>.
</span>
<span class="d-flex align-items-center">
    {{ $activity->created_at->diffForHumans() }}
</span>
@endslot
@slot('body')
<p class="card-text">
    {{ $activity->subject->body }}
</p>
@endslot
@endcomponent
