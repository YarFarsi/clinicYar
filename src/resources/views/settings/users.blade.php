@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">کاربران</h1>
@foreach($users as $u)
    <div class="bg-white border rounded-xl p-3 mb-2 text-sm">{{ $u->name }} — {{ $u->mobile }} — {{ \App\Enums\UserRole::from($u->pivot->role)->label() }}</div>
@endforeach
@endsection
