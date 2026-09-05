@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">تنظیمات کلینیک</h1>
<form method="post" class="bg-white border rounded-xl p-4 max-w-xl space-y-3">
    @csrf
    <input name="name" value="{{ $clinic->name }}" class="w-full border rounded px-3 py-2">
    <input name="phone" value="{{ $clinic->phone }}" class="w-full border rounded px-3 py-2" placeholder="تلفن">
    <input name="email" value="{{ $clinic->email }}" class="w-full border rounded px-3 py-2" placeholder="ایمیل">
    <input name="address" value="{{ $clinic->address }}" class="w-full border rounded px-3 py-2" placeholder="آدرس">
    <input name="city" value="{{ $clinic->city }}" class="w-full border rounded px-3 py-2" placeholder="شهر">
    <input name="timezone" value="{{ $clinic->timezone }}" class="w-full border rounded px-3 py-2">
    <select name="currency" class="w-full border rounded px-3 py-2">
        <option value="IRT" @selected($clinic->currency==='IRT')>تومان (IRT)</option>
        <option value="IRR" @selected($clinic->currency==='IRR')>ریال (IRR)</option>
    </select>
    <select name="operation_mode" class="w-full border rounded px-3 py-2">
        @foreach(\App\Enums\OperationMode::cases() as $m)
            <option value="{{ $m->value }}" @selected($clinic->operation_mode===$m)>{{ $m->label() }}</option>
        @endforeach
    </select>
    <button class="bg-teal-700 text-white rounded px-4 py-2">ذخیره</button>
</form>
<div class="mt-4 flex gap-3 text-sm">
    <a href="{{ route('settings.backup') }}" class="text-teal-700">پشتیبان‌گیری</a>
    <a href="{{ route('settings.users') }}" class="text-teal-700">کاربران</a>
    <a href="{{ route('settings.integrations') }}" class="text-teal-700">اتصالات</a>
</div>
@endsection
