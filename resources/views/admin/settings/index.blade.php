@extends('layouts.admin')
@section('title', $title)

@section('content')
    <div class="px-4 py-6 w-full">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{$title}}</h1>
                <div class="flex items-center text-sm text-gray-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Admin</a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span>{{$title}}</span>
                </div>
            </div>
        </div>
        <div class="rounded-lg shadow-lg overflow-hidden">
            <div class="bg-white border-b border-gray-200">
                <nav class="flex space-x-4 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300">
                    <a href="{{ route('admin.settings') }}"
                       class="flex-shrink-0 px-4 py-4 text-sm font-medium border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 {{ request()->routeIs('admin.settings') ? 'border-blue-500 text-blue-600' : 'text-gray-500' }}">
                        <i class="fa-solid fa-gear mr-1"></i>
                        Settings
                    </a>
                    <a href="{{ route('admin.tiers') }}"
                       class="flex-shrink-0 px-4 py-4 text-sm font-medium border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 {{ request()->routeIs('admin.tiers') ? 'border-blue-500 text-blue-600' : 'text-gray-500' }}">
                        <i class="fa-solid fa-layer-group mr-1"></i>
                        License Tiers
                    </a>
                </nav>
            </div>

            <div class="p-4 px-1 pt-2">
                @if(request()->routeIs('admin.settings'))
                    @include('admin.settings.partials.general')
                @else
                    @include('admin.settings.partials.tiers')
                @endif
            </div>
        </div>

    </div>

@endsection

@push('styles')
    <style>
        .primage {
            max-height: 50px !important;
            max-width: 150px !important;
            margin: 0;
        }

    </style>
@endpush
