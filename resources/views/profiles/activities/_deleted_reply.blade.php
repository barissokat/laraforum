@component('profiles.activities.activity')
@slot('heading')
<span>
    {{ $profileUser->name }} deleted a reply.
</span>
<span>
    {{ $activity->created_at->diffForHumans() }}
</span>
@endslot
@slot('body')
@endslot
@endcomponent
