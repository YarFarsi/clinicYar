@extends('layouts.app')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">تقویم</h1>
    <form class="flex gap-2 text-sm">
        <select name="view" class="border rounded px-2 py-1">
            <option value="day" @selected($view==='day')>روز</option>
            <option value="week" @selected($view==='week')>هفته</option>
            <option value="month" @selected($view==='month')>ماه</option>
        </select>
        <input type="date" name="date" value="{{ $date->toDateString() }}" class="border rounded px-2 py-1">
        <select name="doctor_id" class="border rounded px-2 py-1">
            <option value="">همه پزشکان</option>
            @foreach($doctors as $d)
                <option value="{{ $d->id }}" @selected($doctorId==$d->id)>{{ $d->fullName() }}</option>
            @endforeach
        </select>
        <button class="bg-slate-800 text-white rounded px-3">نمایش</button>
    </form>
</div>
<div class="bg-white rounded-xl border p-4">
    @if($view==='day')
        @for($h=8;$h<=20;$h++)
            @foreach([0,30] as $m)
                @php $slot = sprintf('%02d:%02d', $h, $m); @endphp
                <div class="flex gap-3 border-b border-slate-50 py-2 text-sm">
                    <div class="w-14 text-slate-400">{{ $slot }}</div>
                    <div class="flex-1 space-y-1">
                        @foreach($appointments->filter(fn($a) => substr($a->start_time,0,5)===$slot) as $a)
                            <div class="rounded-lg px-3 py-2 bg-{{ $a->status->color() }}-50 border border-{{ $a->status->color() }}-100">
                                {{ $a->patient->fullName() }} · {{ $a->doctor->fullName() }} · {{ $a->service?->name }} · {{ $a->status->label() }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endfor
    @else
        <div class="grid md:grid-cols-3 gap-2">
            @foreach($appointments as $a)
                <div class="border rounded-lg p-3 text-sm">
                    <div class="font-medium">{{ $a->appointment_date->format('Y-m-d') }} {{ substr($a->start_time,0,5) }}</div>
                    <div>{{ $a->patient->fullName() }}</div>
                    <div class="text-slate-500">{{ $a->status->label() }}</div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
