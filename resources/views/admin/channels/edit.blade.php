@extends('admin.layout.app')

@section('administration-content')
<form method="POST" action="{{ route('admin.channels.update', $channel) }}">
    {{ method_field('PATCH') }}
    @include ('admin.channels._form')
</form>
@endsection
