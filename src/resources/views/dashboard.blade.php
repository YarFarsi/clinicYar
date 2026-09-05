@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">داشبورد</h1>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['نوبت‌های امروز', $stats['today_appointments']],
        ['بیماران امروز', $stats['today_patients']],
        ['درآمد امروز', irt($stats['today_revenue'])],
        ['درآمد این ماه', irt($stats['month_revenue'])],
        ['بدهی بیماران', irt($stats['receivables'])],
        ['نوبت لغوشده امروز', $stats['cancelled_today']],
        ['در انتظار', $stats['pending_today']],
        ['پزشکان', $stats['doctors_count']],
        ['پیگیری سررسید', $stats['follow_ups_due']],
        ['سود ماه', irt($stats['profit_month']['net'])],
        ['بیمار جدید', $stats['retention']['new']],
        ['بازگشتی', $stats['retention']['returning']],
    ] as [$label, $value])
        <div class="bg-white rounded-xl border border-slate-100 p-4">
            <div class="text-xs text-slate-500">{{ $label }}</div>
            <div class="text-lg font-semibold mt-1">{{ $value }}</div>
        </div>
    @endforeach
</div>
<div class="grid lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl border p-4">
        <h2 class="font-medium mb-3">نوبت‌های امروز</h2>
        @forelse($stats['today_list'] as $a)
            <div class="flex justify-between text-sm py-2 border-b border-slate-50">
                <span>{{ substr($a->start_time,0,5) }} — {{ $a->patient->fullName() }}</span>
                <span class="text-slate-500">{{ $a->status->label() }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-400">نوبتی برای امروز نیست.</p>
        @endforelse
    </div>
    <div class="bg-white rounded-xl border p-4">
        <h2 class="font-medium mb-3">نوبت‌های پیش‌رو</h2>
        @forelse($stats['upcoming'] as $a)
            <div class="flex justify-between text-sm py-2 border-b border-slate-50">
                <span>{{ $a->appointment_date->format('Y-m-d') }} {{ substr($a->start_time,0,5) }} — {{ $a->patient->fullName() }}</span>
                <span class="text-slate-500">{{ $a->doctor->fullName() }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-400">موردی نیست.</p>
        @endforelse
        <h2 class="font-medium mt-4 mb-3">بیماران اخیر</h2>
        @foreach($stats['recent_patients'] as $p)
            <a href="{{ route('patients.show', $p) }}" class="block text-sm py-1">{{ $p->fullName() }}</a>
        @endforeach
    </div>
</div>
@endsection
