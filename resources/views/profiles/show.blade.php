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

            <div class="card">
                <img class="card-img-top" src="{{ $profileUser->avatar() }}" alt="{{ $profileUser->name }}">
                <div class="card-body">
                    @can('update', $profileUser)
                    <hr>
                    <form action="{{ route('avatar.store', $profileUser) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" class="form-control-file" name="avatar">
                            @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Add Avatar</button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
