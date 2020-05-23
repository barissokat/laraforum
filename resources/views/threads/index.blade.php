@extends('layouts.app')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-3">Forum Threads</h1>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @include('threads._list')
            <hr>
            {{ $threads->links() }}
        </div>
        <div class="col-md-4">
            <div class="card">
                <img class="card-img-top" src="holder.js/100px180/" alt="">
                <div class="card-body">
                    <h4 class="card-title">Searching</h4>
                    <form action="/threads/search" method="get">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search for something..." name="q">
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary btn-block" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            @if (count($trending))
            <div class="card">
                <img class="card-img-top" src="holder.js/100x180/" alt="">
                <div class="card-header">
                    Trending Threads
                </div>
                <div class="card-body">
                    @foreach ($trending as $thread)
                    <ul class="list-group">
                        <li class="list-group-item">
                            <a href="{{ url($thread->path) }}">
                                {{ $thread->title }}
                            </a>
                        </li>
                    </ul>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
