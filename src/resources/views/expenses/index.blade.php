@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">هزینه‌ها</h1>
<div class="grid sm:grid-cols-3 gap-3 mb-4">
    <div class="bg-white border rounded-xl p-4">درآمد ماه: {{ irt($profit['revenue']) }}</div>
    <div class="bg-white border rounded-xl p-4">هزینه: {{ irt($profit['expenses']) }}</div>
    <div class="bg-white border rounded-xl p-4">سود: {{ irt($profit['net']) }}</div>
</div>
<form method="post" class="bg-white border rounded-xl p-4 mb-4 grid sm:grid-cols-5 gap-2 text-sm">
    @csrf
    <select name="category_id" class="border rounded px-2 py-2">
        @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
    </select>
    <input name="amount" type="number" placeholder="مبلغ" class="border rounded px-2 py-2" required>
    <input name="expense_date" type="date" value="{{ now()->toDateString() }}" class="border rounded px-2 py-2">
    <input name="description" placeholder="شرح" class="border rounded px-2 py-2">
    <button class="bg-teal-700 text-white rounded">ثبت هزینه</button>
</form>
<table class="w-full text-sm bg-white border rounded-xl">
    @foreach($expenses as $e)
        <tr class="border-t"><td class="p-3">{{ $e->category?->name }}</td><td class="p-3">{{ irt($e->amount) }}</td><td class="p-3">{{ $e->expense_date->format('Y-m-d') }}</td></tr>
    @endforeach
</table>
@endsection
