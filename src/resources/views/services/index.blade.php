@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">خدمات و تعرفه‌ها</h1>
<form method="post" class="bg-white border rounded-xl p-4 mb-4 grid sm:grid-cols-5 gap-2 text-sm">
    @csrf
    <input name="name" placeholder="نام خدمت" class="border rounded px-3 py-2" required>
    <input name="category" placeholder="دسته" class="border rounded px-3 py-2">
    <input name="duration_minutes" type="number" value="30" class="border rounded px-3 py-2">
    <input name="price" type="number" placeholder="قیمت تومان" class="border rounded px-3 py-2" required>
    <button class="bg-teal-700 text-white rounded">ثبت</button>
</form>
<table class="w-full text-sm bg-white border rounded-xl overflow-hidden">
    <thead class="bg-slate-50"><tr><th class="text-right p-3">خدمت</th><th class="text-right p-3">مدت</th><th class="text-right p-3">قیمت</th></tr></thead>
    @foreach($services as $s)
        <tr class="border-t"><td class="p-3">{{ $s->name }}</td><td class="p-3">{{ $s->duration_minutes }} دقیقه</td><td class="p-3">{{ irt($s->price) }}</td></tr>
    @endforeach
</table>
@endsection
