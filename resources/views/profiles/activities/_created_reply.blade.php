@component('profiles.activities.activity')
@slot('heading')
<span class="d-flex align-items-center">
    <i class="fas fa-comment-dots fa-2x mr-2"></i>
    <span class="mr-1">
        {{ $profileUser->username }} replied to
    </span>
    <a href="{{$activity->subject->thread->path() }}">
        "{{ $activity->subject->thread->title }}"
    </a>.
</span>
<span class="d-flex align-items-center">
    {{ $activity->created_at->diffForHumans() }}
</span>
@endslot
@slot('body')
<div class="card-text">
    {!! $activity->subject->body !!}
</div>
@endslot
@endcomponent
