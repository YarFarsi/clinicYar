@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">پزشکان</h1>
<form method="post" class="bg-white border rounded-xl p-4 mb-4 grid sm:grid-cols-4 gap-2">
    @csrf
    <input name="first_name" placeholder="نام" class="border rounded px-3 py-2 text-sm" required>
    <input name="last_name" placeholder="نام خانوادگی" class="border rounded px-3 py-2 text-sm" required>
    <input name="specialty" placeholder="تخصص" class="border rounded px-3 py-2 text-sm">
    <button class="bg-teal-700 text-white rounded text-sm">ثبت پزشک</button>
</form>
<div class="grid md:grid-cols-2 gap-3">
@foreach($doctors as $d)
    <a href="{{ route('doctors.show', $d) }}" class="bg-white border rounded-xl p-4 block">
        <div class="font-medium">{{ $d->fullName() }}</div>
        <div class="text-sm text-slate-500">{{ $d->specialty }}</div>
    </a>
@endforeach
</div>
@endsection
