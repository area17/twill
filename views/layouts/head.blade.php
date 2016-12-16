<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" data-behavior="csrf_token">
<title>{{ config('app.name') }}</title>
<link href="/assets/admin/a17cms.css" rel="stylesheet" />
<script src="/assets/admin/a17cms.js"></script>
@yield('extra_css')
@yield('extra_js')
