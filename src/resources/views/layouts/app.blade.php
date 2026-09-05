<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ClinicCRM' }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f766e">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50">
<div id="offline-banner" class="hidden bg-amber-50 text-amber-900 text-sm px-4 py-2 text-center border-b border-amber-200">
    اتصال اینترنت برقرار نیست؛ سیستم همچنان به صورت محلی فعال است.
    @if(($pendingSync ?? 0) > 0)
        <span class="font-medium">{{ $pendingSync }} عملیات در انتظار همگام‌سازی</span>
    @endif
</div>
<div class="flex min-h-screen">
    <aside class="hidden md:flex w-60 shrink-0 flex-col bg-teal-800 text-teal-50">
        <div class="px-5 py-5 border-b border-teal-700">
            <div class="text-lg font-semibold">ClinicCRM</div>
            <div class="text-xs text-teal-200 mt-1">{{ $currentClinic->name ?? '' }}</div>
        </div>
        @php
            $nav = [
                ['dashboard', 'داشبورد', '/dashboard'],
                ['appointments.index', 'نوبت‌ها', '/appointments'],
                ['calendar', 'تقویم', '/calendar'],
                ['waiting-room', 'صف انتظار', '/waiting-room'],
                ['patients.index', 'بیماران', '/patients'],
                ['doctors.index', 'پزشکان', '/doctors'],
                ['services.index', 'خدمات', '/services'],
                ['records.index', 'پرونده‌ها', '/records'],
                ['payments.index', 'پرداخت‌ها', '/payments'],
                ['expenses.index', 'هزینه‌ها', '/expenses'],
                ['reports.index', 'گزارش‌ها', '/reports'],
                ['follow-ups.index', 'پیگیری‌ها', '/follow-ups'],
                ['settings.integrations', 'اتصالات', '/settings/integrations'],
                ['settings.index', 'تنظیمات', '/settings'],
            ];
        @endphp
        <nav class="flex-1 py-3 overflow-y-auto text-sm">
            @foreach($nav as [$name, $label, $href])
                <a href="{{ $href }}" class="block px-5 py-2 {{ request()->routeIs($name) ? 'bg-teal-700 text-white' : 'hover:bg-teal-700/60' }}">{{ $label }}</a>
            @endforeach
        </nav>
    </aside>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center gap-4 px-4">
            <div class="font-medium text-slate-700 hidden sm:block">{{ $currentClinic->name ?? 'کلینیک' }}</div>
            <form action="{{ route('search') }}" class="flex-1 max-w-md">
                <input name="q" value="{{ request('q') }}" placeholder="جستجوی بیمار، پزشک، خدمت..." class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm bg-slate-50">
            </form>
            <a href="{{ route('notifications.index') }}" class="text-sm text-slate-500">اعلان‌ها</a>
            <div class="flex items-center gap-2 text-sm">
                <span id="online-dot" class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                <span id="online-label">آنلاین</span>
            </div>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-slate-500">خروج</button>
            </form>
        </header>
        <main class="p-5">
            @if(session('ok'))
                <div class="mb-4 rounded-lg bg-emerald-50 text-emerald-800 px-4 py-2 text-sm">{{ session('ok') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-lg bg-rose-50 text-rose-800 px-4 py-2 text-sm">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@include('partials.appointment-modal')
</body>
</html>
