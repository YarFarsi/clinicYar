@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-2">اتصالات</h1>
<p class="text-sm text-slate-500 mb-4">حالت فعلی: {{ $clinic->operation_mode->label() }}. در حالت فقط محلی هیچ سرویس خارجی فراخوانی نمی‌شود.</p>
<form method="post" action="{{ route('settings.wordpress') }}" class="bg-white border rounded-xl p-4 max-w-xl space-y-3">
    @csrf
    <h2 class="font-medium">وردپرس</h2>
    <input name="site_url" placeholder="https://clinic-site.example" class="w-full border rounded px-3 py-2">
    <input name="username" placeholder="نام کاربری" class="w-full border rounded px-3 py-2">
    <input name="application_password" type="password" placeholder="Application Password (نه رمز اصلی)" class="w-full border rounded px-3 py-2">
    <button class="bg-teal-700 text-white rounded px-4 py-2 text-sm">ذخیره اتصال</button>
</form>
<form method="post" action="{{ route('sync.run') }}" class="mt-4">@csrf<button class="text-sm border rounded px-3 py-2">پردازش صف همگام‌سازی</button></form>
@endsection
