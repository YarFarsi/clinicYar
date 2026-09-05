@extends('layouts.app')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">نوبت‌ها</h1>
    <form class="flex gap-2">
        <input type="date" name="date" value="{{ $date }}" class="border rounded-lg px-3 py-1.5 text-sm">
        <button class="text-sm bg-slate-800 text-white rounded-lg px-3">نمایش</button>
    </form>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500"><tr>
            <th class="text-right p-3">ساعت</th><th class="text-right p-3">بیمار</th><th class="text-right p-3">پزشک</th><th class="text-right p-3">خدمت</th><th class="text-right p-3">وضعیت</th><th></th>
        </tr></thead>
        <tbody>
        @forelse($appointments as $a)
            <tr class="border-t">
                <td class="p-3">{{ substr($a->start_time,0,5) }}–{{ substr($a->end_time,0,5) }}</td>
                <td class="p-3"><a class="text-teal-700" href="{{ route('patients.show', $a->patient) }}">{{ $a->patient->fullName() }}</a></td>
                <td class="p-3">{{ $a->doctor->fullName() }}</td>
                <td class="p-3">{{ $a->service?->name }}</td>
                <td class="p-3">{{ $a->status->label() }}</td>
                <td class="p-3">
                    <form method="post" action="{{ route('appointments.status', $a) }}" class="flex gap-1">
                        @csrf @method('patch')
                        <select name="status" class="border rounded text-xs">
                            @foreach(\App\Enums\AppointmentStatus::cases() as $s)
                                <option value="{{ $s->value }}" @selected($a->status===$s)>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        <button class="text-xs">ثبت</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="p-6 text-center text-slate-400">نوبتی نیست.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
