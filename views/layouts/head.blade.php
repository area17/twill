<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" data-behavior="csrf_token">
<title>{{ config('app.name') }}</title>
@stack('extra_css')
@stack('extra_js')
