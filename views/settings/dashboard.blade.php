@extends('twill::layouts.free')

@section('content')
    <div class="container">
        @foreach ($groups as $settingGroup)
            <div>
                <a href="{{ $settingGroup->getHref() }}">
                    <h4>{{ $settingGroup->getLabel() }}</h4>
                    @if ($settingGroup->getDescription())
                        {{ $settingGroup->getDescription() }}
                    @endif
                </a>
                @if (!$loop->last)
                    <hr />
                @endif
            </div>
        @endforeach
    </div>
@endsection
