@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">پرداخت‌ها</h1>
<table class="w-full text-sm bg-white border rounded-xl">
    <thead class="bg-slate-50"><tr><th class="text-right p-3">بیمار</th><th class="text-right p-3">مبلغ</th><th class="text-right p-3">روش</th><th class="text-right p-3">تاریخ</th></tr></thead>
    @foreach($payments as $p)
        <tr class="border-t">
            <td class="p-3">{{ $p->patient->fullName() }}</td>
            <td class="p-3">{{ irt($p->amount) }}</td>
            <td class="p-3">{{ $p->payment_method->label() }}</td>
            <td class="p-3">{{ optional($p->paid_at)->format('Y-m-d') }}</td>
        </tr>
    @endforeach
</table>
{{ $payments->links() }}
@endsection
