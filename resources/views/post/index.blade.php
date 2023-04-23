@extends('post/layout')

@section('tittle', 'Semua post')

@push('styles')
    <style>
        table tr td:first-child {
            width: 20%;
            word-break: break-word;
        }
        table tr td:last-child {
            width: 20%;
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <h1 class="text-center">Semua post</h1>
    @if(session('message'))
    <div class="alert alert-info">
        {{ session('message') }}
    </div>
    @endif
    <a href="{{ route('post.create') }}" class="btn btn-success">Buat post baru</a>
    <table width="100%" cellpadding="5" class="table-bordered mt-2">
        <thead>
            <tr>
                <th>Tittle</th>
                <th>Body</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr>
                <td>{{ $post->tittle }}</td>
                <td>{{ $post->body }}</td>
                <td>
                    <a href="{{ route('post.edit', $post->id) }}" class="btn btn-primary">Edit</a>
                    
                    <form class="d-inline-block" action="{{ url('post', $post->id) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button href="{{ route('post.destroy', $post->id) }}" class="btn btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-end link-secondary mt-3">
        <a href="{{ route('post.spa') }}">Open in single page</a>
    </div>
@endsection