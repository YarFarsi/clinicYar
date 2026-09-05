@extends('layouts.app')
@section('content')
<div class="flex justify-between mb-4">
    <div>
        <h1 class="text-xl font-semibold">{{ $patient->fullName() }}</h1>
        <p class="text-sm text-slate-500">موبایل: {{ $patient->mobile }}</p>
    </div>
</div>
<div class="grid sm:grid-cols-3 gap-3 mb-6">
    <div class="bg-white border rounded-xl p-4"><div class="text-xs text-slate-500">خدمات</div><div class="font-semibold">{{ irt($balance['charges']) }}</div></div>
    <div class="bg-white border rounded-xl p-4"><div class="text-xs text-slate-500">پرداخت</div><div class="font-semibold">{{ irt($balance['paid']) }}</div></div>
    <div class="bg-white border rounded-xl p-4"><div class="text-xs text-slate-500">بدهی</div><div class="font-semibold">{{ irt($balance['balance']) }}</div></div>
</div>
<div class="grid lg:grid-cols-2 gap-4">
    <div class="bg-white border rounded-xl p-4">
        <h2 class="font-medium mb-3">ثبت پرداخت</h2>
        <form method="post" action="{{ route('payments.store') }}" class="space-y-2">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            <input name="amount" type="number" step="0.01" placeholder="مبلغ (تومان)" class="w-full border rounded px-3 py-2 text-sm" required>
            <select name="payment_method" class="w-full border rounded px-3 py-2 text-sm">
                @foreach(\App\Enums\PaymentMethod::cases() as $m)
                    <option value="{{ $m->value }}">{{ $m->label() }}</option>
                @endforeach
            </select>
            <button class="bg-teal-700 text-white rounded px-4 py-2 text-sm">ثبت</button>
        </form>
        @can('permission', 'medical_records.create')
        <h2 class="font-medium mt-6 mb-3">پرونده پزشکی</h2>
        <form method="post" action="{{ route('records.store') }}" class="space-y-2">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            <input type="hidden" name="doctor_id" value="{{ $patient->appointments->last()?->doctor_id ?? optional(\App\Models\Doctor::query()->first())->id }}">
            <textarea name="chief_complaint" placeholder="شکایت اصلی" class="w-full border rounded px-3 py-2 text-sm"></textarea>
            <textarea name="diagnosis" placeholder="تشخیص (ثبت پزشک، نه پیشنهاد سیستم)" class="w-full border rounded px-3 py-2 text-sm"></textarea>
            <textarea name="treatment" placeholder="اقدامات" class="w-full border rounded px-3 py-2 text-sm"></textarea>
            <button class="bg-slate-800 text-white rounded px-4 py-2 text-sm">ذخیره پرونده</button>
        </form>
        @endcan
    </div>
    <div class="bg-white border rounded-xl p-4">
        <h2 class="font-medium mb-3">خط زمانی</h2>
        @foreach($timeline as $row)
            <div class="border-r-2 border-teal-200 pr-3 py-2 text-sm">
                <div class="text-xs text-slate-400">{{ optional($row['at'])->format('Y/m/d') }}</div>
                @if($row['type']==='appointment') ویزیت / نوبت — {{ $row['item']->status->label() }}
                @elseif($row['type']==='payment') پرداخت {{ irt($row['item']->amount) }}
                @elseif($row['type']==='record') پرونده پزشکی
                @else پیگیری: {{ $row['item']->title }}
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
