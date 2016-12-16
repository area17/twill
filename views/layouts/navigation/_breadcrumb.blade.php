@if(isset($breadcrumb))
    @php
        $chevron = '';
    @endphp
    <div class="breadcrumb" style="padding: 0px 0px 30px 0px;">
        @foreach($breadcrumb as $label => $link)
            @if($link != null)
                {{ $chevron }}<a href="{!! $link !!}">{{ $label }}</a>
            @else
                {{ $chevron }}<span>{{ $label }}</span>
            @endif
            @php
                $chevron = ' → ';
            @endphp
        @endforeach
    </div>
@endif
