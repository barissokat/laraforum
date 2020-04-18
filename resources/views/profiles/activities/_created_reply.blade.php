@component('profiles.activities.activity')
@slot('heading')
<span>
    {{ $profileUser->name }} replied to
    <a href="{{$activity->subject->thread->path() }}">
        "{{ $activity->subject->thread->title }}"
    </a>.
</span>
<span>
    {{ $activity->created_at->diffForHumans() }}
</span>
@endslot
@slot('body')
<p class="card-text">
    {{ $activity->subject->body }}
</p>
@endslot
@endcomponent
