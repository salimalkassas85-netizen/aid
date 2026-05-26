<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'اصنع تهنئة عيد الأضحى باسم من تحب')</title>
    <meta name="description" content="@yield('description', 'أنشئ تهنئة عيد الأضحى باسم شخص عزيز وشاركها برابط خاص على فيسبوك.')">
    @yield('meta')
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/css/eid.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/eid.css') }}">
    @endif
    @if (file_exists(resource_path('css/eid.css')))
        <style>
            {!! file_get_contents(resource_path('css/eid.css')) !!}
        </style>
    @endif
</head>
<body class="eid-body">
    <div class="eid-background" aria-hidden="true">
        <span class="orb orb-one"></span>
        <span class="orb orb-two"></span>
        <span class="crescent"></span>
        <span class="lantern lantern-one"></span>
        <span class="lantern lantern-two"></span>
        <span class="star star-one"></span>
        <span class="star star-two"></span>
        <span class="star star-three"></span>
        <span class="star star-four"></span>
    </div>

    <main>
        @yield('content')
    </main>

    <footer class="eid-footer">
        Developed by Salem
    </footer>

    @stack('scripts')
</body>
</html>
