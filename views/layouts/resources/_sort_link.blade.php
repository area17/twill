@php
    $sortOrder = Request::has('sortOrder') && (Request::input('sortField') == $column['field'] || (isset($column['sortField']) && Request::input('sortField') == $column['sortField']))
@endphp

<a href="{{ Request::url() }}?sortField={{ $column['sortField'] or $column['field'] }}&sortOrder={{ $sortOrder ? (Request::input('sortOrder') == 'asc' ? 'desc' : 'asc') : 'desc' }}" class="icon-sort{{ $sortOrder ? (Request::input('sortOrder') == 'asc' ? '-desc' : '-asc') : '' }}">{{ $column['title'] }}</a>
