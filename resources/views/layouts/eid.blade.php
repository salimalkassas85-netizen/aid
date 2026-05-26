<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'اصنع تهنئة عيد الأضحى باسم من تحب')</title>
    <meta name="description" content="@yield('description', 'أنشئ تهنئة عيد الأضحى باسم شخص عزيز وشاركها برابط خاص على فيسبوك.')">
    @yield('meta')
    @vite(['resources/css/app.css', 'resources/css/eid.css', 'resources/js/app.js'])
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

    @stack('scripts')
</body>
</html>
