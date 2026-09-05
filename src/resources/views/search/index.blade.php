@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">جستجو: {{ $q }}</h1>
<h2 class="font-medium mt-4">بیماران</h2>
@foreach($results['patients'] as $p)
    <a href="{{ route('patients.show', $p) }}" class="block py-1">{{ $p->fullName() }} — {{ $p->mobile }}</a>
@endforeach
<h2 class="font-medium mt-4">پزشکان</h2>
@foreach($results['doctors'] as $d)
    <a href="{{ route('doctors.show', $d) }}" class="block py-1">{{ $d->fullName() }}</a>
@endforeach
@endsection
