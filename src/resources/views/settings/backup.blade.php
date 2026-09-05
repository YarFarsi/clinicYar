@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">پشتیبان‌گیری</h1>
<form method="post" class="mb-4">@csrf<button class="bg-teal-700 text-white rounded px-4 py-2 text-sm">ایجاد Backup</button></form>
@foreach($backups as $b)
    <div class="bg-white border rounded-xl p-3 mb-2 flex justify-between text-sm">
        <span>{{ $b->created_at }} · {{ number_format($b->size/1024,1) }} KB</span>
        <a class="text-teal-700" href="{{ route('settings.backup.download', $b) }}">دانلود</a>
    </div>
@endforeach
@endsection
