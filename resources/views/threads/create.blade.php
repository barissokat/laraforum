@extends('layouts.app')

@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-8">
         <div class="card">
            <div class="card-header">Create a New Thread</div>

            <div class="card-body">
               @if (session('status'))
               <div class="alert alert-success" role="alert">
                  {{ session('status') }}
               </div>
               @endif

               <form action="{{ route('threads.store')}}" method="post">
                  @csrf
                  <div class="form-group">
                     <label for="title">Title</label>
                     <input type="text" class="form-control" name="title" id="title" aria-describedby="helpTitle">
                     @error('title')
                     <div class="invalid-feedback">
                        {{ $message }}
                     </div>
                     @enderror
                  </div>
                  <div class="form-group">
                     <textarea class="form-control" name="body" id="body" rows="8"
                        placeholder="Have something to say?"></textarea>
                     @error('body')
                     <div class="invalid-feedback">
                        {{ $message }}
                     </div>
                     @enderror
                  </div>
                  <button type="submit" class="btn btn-primary">Publish</button>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection