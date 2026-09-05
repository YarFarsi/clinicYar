@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">راه‌اندازی کلینیک</h1>
<div class="space-y-4 max-w-xl">
    <div class="bg-white border rounded-xl p-4">
        <h2 class="font-medium mb-2">۱) اطلاعات کلینیک</h2>
        <p class="text-sm">{{ $clinic->name }}</p>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <h2 class="font-medium mb-2">۲) افزودن پزشک</h2>
        <form method="post" action="{{ route('onboarding.doctor') }}" class="grid grid-cols-3 gap-2 text-sm">
            @csrf
            <input name="first_name" placeholder="نام" class="border rounded px-2 py-1" required>
            <input name="last_name" placeholder="نام خانوادگی" class="border rounded px-2 py-1" required>
            <button class="bg-teal-700 text-white rounded">افزودن</button>
        </form>
        <p class="text-xs mt-2">{{ $doctors->count() }} پزشک</p>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <h2 class="font-medium mb-2">۳) خدمت</h2>
        <form method="post" action="{{ route('onboarding.service') }}" class="grid grid-cols-4 gap-2 text-sm">
            @csrf
            <input name="name" placeholder="ویزیت" class="border rounded px-2 py-1" required>
            <input name="duration_minutes" value="30" class="border rounded px-2 py-1">
            <input name="price" value="500000" class="border rounded px-2 py-1">
            <button class="bg-teal-700 text-white rounded">افزودن</button>
        </form>
    </div>
    <div class="bg-white border rounded-xl p-4">
        <h2 class="font-medium mb-2">۴) ساعات کاری</h2>
        <form method="post" action="{{ route('onboarding.hours') }}" class="grid grid-cols-5 gap-2 text-sm">
            @csrf
            <select name="doctor_id" class="border rounded px-2 py-1">
                @foreach($doctors as $d)<option value="{{ $d->id }}">{{ $d->fullName() }}</option>@endforeach
            </select>
            <select name="day_of_week"><option value="6">شنبه</option></select>
            <input type="time" name="start_time" value="09:00">
            <input type="time" name="end_time" value="13:00">
            <input type="hidden" name="slot_duration" value="30">
            <button class="bg-slate-800 text-white rounded">ثبت</button>
        </form>
    </div>
    <a href="{{ route('dashboard') }}" class="inline-block bg-teal-700 text-white rounded-lg px-4 py-2 text-sm">۵ و ۶) رفتن به داشبورد و ثبت نوبت</a>
</div>
@endsection
