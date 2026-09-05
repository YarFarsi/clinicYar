@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">گزارش‌ها</h1>
<form class="flex gap-2 mb-4 text-sm">
    <input type="date" name="from" value="{{ $from }}" class="border rounded px-2 py-1">
    <input type="date" name="to" value="{{ $to }}" class="border rounded px-2 py-1">
    <button class="bg-slate-800 text-white rounded px-3">اعمال</button>
</form>
<h2 class="font-medium mb-2">عملکرد پزشک</h2>
<table class="w-full text-sm bg-white border rounded-xl mb-6">
    <thead class="bg-slate-50"><tr><th class="p-3 text-right">پزشک</th><th>نوبت</th><th>تکمیل</th><th>لغو</th><th>عدم حضور</th><th>درآمد</th></tr></thead>
    @foreach($doctors as $row)
        <tr class="border-t text-center"><td class="p-3 text-right">{{ $row['doctor'] }}</td><td>{{ $row['appointments'] }}</td><td>{{ $row['completed'] }}</td><td>{{ $row['cancelled'] }}</td><td>{{ $row['no_show'] }}</td><td>{{ irt($row['revenue']) }}</td></tr>
    @endforeach
</table>
<h2 class="font-medium mb-2">عملکرد خدمت</h2>
<table class="w-full text-sm bg-white border rounded-xl">
    @foreach($services as $row)
        <tr class="border-t"><td class="p-3">{{ $row['service'] }}</td><td>{{ $row['count'] }}</td><td>{{ irt($row['revenue']) }}</td><td>{{ irt($row['average']) }}</td></tr>
    @endforeach
</table>
@endsection
