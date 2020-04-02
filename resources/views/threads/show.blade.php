@extends('layouts.app')

@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-8">
         <div class="card">
            <div class="card-header">
               <h3><a href="#">{{ $thread->owner->name }}</a> posted: {{ $thread->title }}</h3>
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
            </div>
         </div>
         <hr>
         <div class="card">
            <div class="card-header">
               <h2>Replies</h2>
            </div>

            <div class="card-body">
               @include('threads._replies')
            </div>


            <div class="card-footer text-muted">
               @auth
               <form action="{{ route('replies.store', $thread )}}" method="post">
                  @csrf
                  <div class="form-group">
                     <textarea class="form-control" name="body" id="body" rows="3"
                        placeholder="Have something to say?"></textarea>
                     @error('body')
                     <div class="invalid-feedback">
                        {{ $message }}
                     </div>
                     @enderror
                  </div>
                  <button type="submit" class="btn btn-primary">Post</button>
               </form>
               @else
               <p class="text-muted text-center">
                  Please <a href="{{ route('login') }}">sign in</a> to participate in this discussion.
               </p>
               @endauth
            </div>
         </div>
      </div>
   </div>
</div>
@endsection