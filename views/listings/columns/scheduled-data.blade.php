<span>
    {{-- Replaces TableCellDates.vue --}}
    @if ($label)
        <span class="tablecell__datePub @if($isExpired) s--expired @endif">
            {{$startDate}}
            @if ($endDate)
                - {{$endDate}}
            @endif
            @if ($label)
                <br>
                <span>{{ $label }}</span>
            @endif
        </span>
    @elseif ($hasStartDate)
        -
    @else
        {{$startDate}}
    @endif
  </span>
