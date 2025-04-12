@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Create Access Key</h1>

    <form method="POST" action="{{ route('admin.access-keys.store') }}">
        @csrf

        @include('admin.access-keys.partials.form', ['key' => null])

        <button type="submit" class="btn btn-primary mt-4">Create Key</button>
    </form>
@endsection
