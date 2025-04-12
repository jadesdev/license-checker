@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Access Key</h1>

    <form method="POST" action="{{ route('admin.access-keys.update', $key->id) }}">
        @csrf
        @method('PUT')

        @include('admin.access-keys.partials.form', ['key' => $key, 'domainsText' => $domainsText])

        <button type="submit" class="btn btn-primary mt-4">Update Key</button>
    </form>
@endsection

@section('title', 'Edit Access key')
