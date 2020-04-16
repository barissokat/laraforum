@extends('layouts.app')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-3">
            {{ $profileUser->name }}</h1>
        <p class="lead">
            <small>Since {{ $profileUser->created_at->diffForHumans() }}</small>
        </p>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @foreach ($threads as $thread)
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">{{ $thread->title }}</h4>
                    <small>Since {{ $thread->created_at->diffForHumans() }}</small>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        {{ $thread->body }}
                    </p>
                </div>
            </div>
            @endforeach

            {{ $threads->links() }}
        </div>
        <div class="col-md-2">
            <div class="card text-left">
              <img class="card-img-top" src="holder.js/100px180/" alt="">
              <div class="card-body">
                <h4 class="card-title">Title</h4>
                <p class="card-text">Body</p>
              </div>
            </div>
        </div>
    </div>
</div>
@endsection
