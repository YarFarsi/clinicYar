<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ClinicCRM — مدیریت کلینیک</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f766e">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    {{ $slot }}
</body>
</html>
