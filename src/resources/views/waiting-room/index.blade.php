@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">صف انتظار امروز</h1>
<div class="space-y-2">
@forelse($appointments as $a)
    <div class="bg-white border rounded-xl p-4 flex items-center justify-between">
        <div>
            <div class="text-sm text-slate-400">{{ substr($a->start_time,0,5) }}</div>
            <div class="font-medium">{{ $a->patient->fullName() }}</div>
            <div class="text-sm text-slate-500">{{ $a->doctor->fullName() }} · {{ $a->service?->name }}</div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm">{{ $a->status->label() }}</span>
            <form method="post" action="{{ route('appointments.status', $a) }}" class="flex gap-1">
                @csrf @method('patch')
                @if($a->status->value==='scheduled' || $a->status->value==='confirmed')
                    <input type="hidden" name="status" value="arrived">
                    <button class="text-xs bg-amber-100 text-amber-800 rounded px-3 py-1">ورود به انتظار</button>
                @elseif($a->status->value==='arrived')
                    <input type="hidden" name="status" value="in_progress">
                    <button class="text-xs bg-violet-100 text-violet-800 rounded px-3 py-1">شروع ویزیت</button>
                @elseif($a->status->value==='in_progress')
                    <a href="{{ route('records.create', ['appointment_id'=>$a->id]) }}" class="text-xs bg-teal-100 text-teal-800 rounded px-3 py-1">پرونده</a>
                    <input type="hidden" name="status" value="completed">
                    <button class="text-xs bg-emerald-100 text-emerald-800 rounded px-3 py-1">اتمام</button>
                @endif
            </form>
        </div>
    </div>
@empty
    <p class="text-slate-400">نوبتی برای امروز نیست.</p>
@endforelse
</div>
@endsection
