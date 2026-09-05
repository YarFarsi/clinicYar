@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">ثبت پرونده</h1>
<form method="post" action="{{ route('records.store') }}" class="bg-white border rounded-xl p-4 max-w-xl space-y-3">
    @csrf
    <input type="hidden" name="patient_id" value="{{ $appointment?->patient_id }}">
    <input type="hidden" name="doctor_id" value="{{ $appointment?->doctor_id }}">
    <input type="hidden" name="appointment_id" value="{{ $appointment?->id }}">
    <p class="text-sm">{{ $appointment?->patient?->fullName() }} — {{ $appointment?->doctor?->fullName() }}</p>
    <textarea name="chief_complaint" class="w-full border rounded p-2" placeholder="شکایت اصلی"></textarea>
    <textarea name="diagnosis" class="w-full border rounded p-2" placeholder="تشخیص پزشک"></textarea>
    <textarea name="treatment" class="w-full border rounded p-2" placeholder="اقدام درمانی"></textarea>
    <textarea name="notes" class="w-full border rounded p-2" placeholder="یادداشت"></textarea>
    <button class="bg-teal-700 text-white rounded px-4 py-2">ذخیره</button>
</form>
@endsection
