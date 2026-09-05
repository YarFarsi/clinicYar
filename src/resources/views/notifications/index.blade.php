@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">اعلان‌ها</h1>
@foreach($items as $n)
    <div class="bg-white border rounded-xl p-3 mb-2 text-sm">{{ $n->title }} — {{ $n->body }}</div>
@endforeach
@endsection
