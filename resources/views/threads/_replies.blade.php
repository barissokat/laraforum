@forelse ($replies as $reply)
<h5 class="card-title"> <a href="#">{{ $reply->owner->name }}</a> said {{ $reply->created_at->diffForHumans() }}</h5>
<p class="card-text">
    {{ $reply->body }}
</p>
@if(!$loop->last)
<hr>
@endif
@empty
<p>Reply not found!</p>
@endforelse
