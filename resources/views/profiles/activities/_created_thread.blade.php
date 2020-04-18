@component('profiles.activities.activity')
@slot('heading')
<span>
    {{ $profileUser->name }} published
    <a href="{{$activity->subject->path() }}">
        "{{ $activity->subject->title }}"
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
