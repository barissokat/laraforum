@extends('layouts.app')

@section('head')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

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

                    <form action="{{ route('threads.store')}}" method="post" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-group">
                            <label for="channel">Channels</label>
                            <select class="custom-select @error('channel_id') is-invalid @enderror" name="channel_id"
                                id="channel" required>
                                <option value="">Choose One...</option>
                                @foreach ($channels as $channel)
                                <option value="{{ $channel->id }}"
                                    {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                    {{ $channel->name }}
                                </option>
                                @endforeach
                            </select>
                            @error(' channel_id') <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                                id="title" aria-describedby="helpTitle" value="{{ old('title') }}" required>
                            @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <textarea class="form-control @error('body') is-invalid @enderror" name="body" id="body"
                                rows="8" placeholder="Have something to say?" required>{{ old('body') }}</textarea>
                            @error('body')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="6Lf2qPkUAAAAAC847rLEkRbotybFDFm2wc4w7PWe"></div>

                        </div>

                        <button type="submit" class="btn btn-primary">Publish</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
