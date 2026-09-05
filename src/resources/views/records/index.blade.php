@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">پرونده‌های پزشکی</h1>
<p class="text-xs text-slate-400 mb-4">سامانه تشخیص یا نسخه معتبر تولید نمی‌کند. محتوا توسط پزشک ثبت می‌شود.</p>
<div class="bg-white border rounded-xl">
@foreach($records as $r)
    <div class="p-4 border-b text-sm">
        <a class="font-medium text-teal-700" href="{{ route('patients.show', $r->patient) }}">{{ $r->patient->fullName() }}</a>
        · {{ $r->doctor->fullName() }} · {{ $r->created_at->format('Y-m-d') }}
    </div>
@endforeach
</div>
{{ $records->links() }}
@endsection
