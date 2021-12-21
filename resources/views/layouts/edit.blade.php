@extends('layouts.app')
@section('tittle', 'Edit post')
@section('content')
<h1 class="text-center">Edit post</h1>
@if ( $errors->any() )
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('posts.update', ['post' => $post->id]) }}" method="post">
    @csrf
    @method('PUT')
    <input type="text" name="tittle" class="form-control" maxlength="150" value="{{ old('tittle', $post->tittle) }}" placeholder="Tittle">
    <textarea name="body" rows="10" class="form-control w-100" placeholder="Body">{{ old('body', $post->body) }}</textarea>
    <button type="submit" class="btn btn-primary w-100">Submit</button>
</form>
@endsection