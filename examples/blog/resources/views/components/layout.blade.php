<!doctype html>
<html lang="en">
<head>
    <title>{{$title ?? 'Twill blog demo'}}</title>
    <link rel="stylesheet" href="{{mix('css/app.css')}}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div>
    <x-page-header/>
    {{ $slot }}
</div>
<script src="{{mix('js/app.js')}}"></script>
</body>
</html>
