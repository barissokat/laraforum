@extends('layouts.app')

@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-8">
         <div class="card">
            <div class="card-header">Forum Threads</div>

            <div class="card-body">
               @if (session('status'))
               <div class="alert alert-success" role="alert">
                  {{ session('status') }}
               </div>
               @endif

               @foreach ($threads as $thread)
               <article>
                  <h4>
                     <a href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a>
                     - by {{ $thread->owner->name}}
                  </h4>
                  <div>
                     {{ $thread->body }}
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