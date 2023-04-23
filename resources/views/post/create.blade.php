@extends('post.layout')
@section('tittle', 'Buat post baru')
@section('content')
<h1 class="text-center">Buat post baru</h1>
@if ( $errors->any() )
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('post.store') }}" method="post">
    @csrf
    <input type="text" name="tittle" class="form-control" placeholder="Tittle" value="{{ old('tittle') }}">
    <textarea name="body" rows="10" class="form-control w-100" placeholder="Body">{{ old('body') }}</textarea>
    <button type="submit" class="btn btn-primary w-100">Buat post</button>
</form>
@endsection