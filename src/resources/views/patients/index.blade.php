@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">بیماران</h1>
</div>
<form class="mb-4"><input name="q" value="{{ $q }}" placeholder="نام، موبایل..." class="border rounded-lg px-3 py-2 w-full max-w-md text-sm"></form>
<form method="post" action="{{ route('patients.store') }}" class="bg-white border rounded-xl p-4 mb-4 grid sm:grid-cols-4 gap-2">
    @csrf
    <input name="first_name" placeholder="نام" class="border rounded px-3 py-2 text-sm" required>
    <input name="last_name" placeholder="نام خانوادگی" class="border rounded px-3 py-2 text-sm" required>
    <input name="mobile" placeholder="موبایل" class="border rounded px-3 py-2 text-sm" required>
    <button class="bg-teal-700 text-white rounded text-sm">ثبت بیمار</button>
</form>
<div class="bg-white border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50"><tr><th class="text-right p-3">نام</th><th class="text-right p-3">موبایل</th><th></th></tr></thead>
        <tbody>
        @foreach($patients as $p)
            <tr class="border-t">
                <td class="p-3">{{ $p->fullName() }}</td>
                <td class="p-3">{{ $p->mobile }}</td>
                <td class="p-3"><a class="text-teal-700" href="{{ route('patients.show', $p) }}">پرونده</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if(method_exists($patients, 'links')) {{ $patients->links() }} @endif
@endsection
