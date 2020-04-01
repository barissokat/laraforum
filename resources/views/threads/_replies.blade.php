<h4>Replies</h4>
@foreach ($thread->replies as $reply)
<article>
   <h6><a href="#">{{ $reply->owner->name }}</a> said {{ $reply->created_at->diffForHumans() }}</h6>
   <div>
      {{ $reply->body }}
   </div>
</article>
<hr>
@endforeach