<form method="POST" action="{{ route('admin.channels.update', $channel) }}">{{ csrf_field() }}
    {{ method_field('PATCH') }}
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $channel->name) }}"
            required>
    </div>

    <div class="form-group">
        <label for="description">Description:</label>
        <input type="text" class="form-control" id="description" name="description"
            value="{{ old('description', $channel->description) }}" required>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Add</button>
    </div>

    @if (count($errors))
    <ul class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif
</form>