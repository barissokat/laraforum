@forelse ($thread->replies as $reply)
<article>
   <h6><a href="#">{{ $reply->owner->name }}</a> said {{ $reply->created_at->diffForHumans() }}</h6>
   <div>
      {{ $reply->body }}
   </div>
</article>
@if(!$loop->last)
<hr>
@endif
@empty
<p>Reply not found!</p>
@endforelse