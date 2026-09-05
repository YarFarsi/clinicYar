@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-2">{{ $doctor->fullName() }}</h1>
<p class="text-slate-500 mb-4">{{ $doctor->specialty }}</p>
<form method="post" action="{{ route('doctors.hours', $doctor) }}" class="bg-white border rounded-xl p-4 mb-4 grid sm:grid-cols-5 gap-2 text-sm">
    @csrf
    <select name="day_of_week" class="border rounded px-2 py-2">
        @foreach([6=>'شنبه',0=>'یکشنبه',1=>'دوشنبه',2=>'سه‌شنبه',3=>'چهارشنبه',4=>'پنجشنبه',5=>'جمعه'] as $n=>$l)
            <option value="{{ $n }}">{{ $l }}</option>
        @endforeach
    </select>
    <input type="time" name="start_time" value="09:00" class="border rounded px-2 py-2">
    <input type="time" name="end_time" value="13:00" class="border rounded px-2 py-2">
    <input type="number" name="slot_duration" value="30" class="border rounded px-2 py-2">
    <button class="bg-slate-800 text-white rounded">ساعت کاری</button>
</form>
<ul class="text-sm space-y-1">
@foreach($doctor->workingHours as $h)
    <li>{{ $h->day_of_week }} · {{ substr($h->start_time,0,5) }}–{{ substr($h->end_time,0,5) }} · {{ $h->slot_duration }} دقیقه</li>
@endforeach
</ul>
@endsection
