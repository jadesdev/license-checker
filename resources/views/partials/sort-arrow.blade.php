@php
    $currentSort = request()->get('sort', 'created_at');
    $currentDirection = request()->get('direction', 'desc');
@endphp

@if($field === $currentSort)
    <i class="fas fa-chevron-{{ $currentDirection === 'asc' ? 'up' : 'down' }} ml-1 text-xs"></i>
@endif
