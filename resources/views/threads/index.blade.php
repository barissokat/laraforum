@extends('layouts.app')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-3">Forum Threads</h1>
    </div>
</div>
<div class="container">
    @include('threads._list')
    <hr>
    {{ $threads->links() }}
</div>
@endsection
