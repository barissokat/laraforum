@component('profiles.activities.activity')
@slot('heading')
<span class="d-flex align-items-center">
    <i class="fas fa-trash fa-2x mr-2"></i>
    {{ $profileUser->username }} deleted a thread.
</span>
<span class="d-flex align-items-center">
    {{ $activity->created_at->diffForHumans() }}
</span>
@endslot
@slot('body')
@endslot
@endcomponent
