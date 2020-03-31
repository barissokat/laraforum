@extends('layouts.app')

@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-8">
         <div class="card">
            <div class="card-header">
               <h3>{{ $thread->title }}</h3>
            </div>

            <div class="card-body">
               @if (session('status'))
               <div class="alert alert-success" role="alert">
                  {{ session('status') }}
               </div>
               @endif
               <article>
                  <div>
                     {{ $thread->body }}
                  </div>
                  <a href="{{ route('threads.index') }}">Back</a>
               </article>
               <hr>
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
            </div>
         </div>
      </div>
   </div>
</div>
@endsection