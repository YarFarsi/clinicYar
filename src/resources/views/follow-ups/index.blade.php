@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">پیگیری‌ها</h1>
<form method="post" class="bg-white border rounded-xl p-4 mb-4 grid sm:grid-cols-4 gap-2 text-sm">
    @csrf
    <select name="patient_id" class="border rounded px-2 py-2">
        @foreach(\App\Models\Patient::query()->orderBy('last_name')->limit(100)->get() as $p)
            <option value="{{ $p->id }}">{{ $p->fullName() }}</option>
        @endforeach
    </select>
    <input name="title" placeholder="عنوان" class="border rounded px-2 py-2" required>
    <input type="datetime-local" name="due_at" class="border rounded px-2 py-2" required>
    <button class="bg-teal-700 text-white rounded">ثبت</button>
</form>
@foreach($items as $i)
    <div class="bg-white border rounded-xl p-3 mb-2 text-sm">{{ $i->title }} — {{ $i->patient->fullName() }} — {{ $i->due_at->format('Y-m-d') }}</div>
@endforeach
@endsection
