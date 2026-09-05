<x-guest-layout>
<div class="min-h-screen flex items-center justify-center p-6">
    <form method="post" class="bg-white border rounded-2xl p-6 max-w-md w-full space-y-3">
        @csrf
        <h1 class="text-xl font-semibold">نصب ClinicCRM</h1>
        <p class="text-sm text-slate-500">PHP {{ $php }}</p>
        @foreach($extensions as $ext=>$ok)
            <div class="text-sm">{{ $ext }}: {{ $ok ? 'OK' : 'Missing' }}</div>
        @endforeach
        <input name="name" placeholder="نام مدیر" class="w-full border rounded px-3 py-2" required>
        <input name="mobile" placeholder="موبایل" class="w-full border rounded px-3 py-2" required>
        <input name="password" type="password" placeholder="رمز" class="w-full border rounded px-3 py-2" required>
        <input name="clinic_name" placeholder="نام کلینیک" class="w-full border rounded px-3 py-2" required>
        <label class="text-sm flex gap-2 items-center"><input type="checkbox" name="seed_demo" value="1"> بارگذاری داده نمونه</label>
        <button class="w-full bg-teal-700 text-white rounded py-2">نصب</button>
    </form>
</div>
</x-guest-layout>
