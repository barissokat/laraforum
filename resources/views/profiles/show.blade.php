@extends('layouts.app')

@section('content')
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-3">
            {{ $profileUser->username }}
        </h1>
        <p class="lead">
            <small> {{ $profileUser->reputation }} XP</small>
            <br>
            <small>Since {{ $profileUser->created_at->diffForHumans() }}</small>
        </p>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @forelse ($activities as $date => $activity)
            <h5 class="mt-4">{{ $date }}</h5>
            @foreach ($activity as $record)
            @if (view()->exists("profiles.activities._{$record->type}"))
            @include("profiles.activities._{$record->type}", ['activity' => $record])
            @endif
            @endforeach
            @empty
            <p>There is no activity for this user yet.</p>
            @endforelse
        </div>
        <div class="col-md-4">
            <avatar-form :user="{{ $profileUser }}"></avatar-form>
        </div>
    </div>
</div>
@endsection
