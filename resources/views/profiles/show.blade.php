@extends('layouts.app')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-3">
            {{ $profileUser->name }}
        </h1>
        <p class="lead">
            <small>Since {{ $profileUser->created_at->diffForHumans() }}</small>
        </p>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @forelse ($activities as $date => $activity)
            <h5>{{ $date }}</h5>
            @foreach ($activity as $record)
            @include("profiles.activities._{$record->type}", ['activity' => $record])
            @endforeach
            @empty
            <p>There are no relevant results at this time.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
