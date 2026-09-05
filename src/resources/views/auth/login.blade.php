<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-6">
        <form method="post" action="{{ route('login') }}" class="w-full max-w-sm bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
            @csrf
            <h1 class="text-xl font-semibold">ورود به کلینیک</h1>
            @if($errors->any())<p class="text-sm text-rose-600">{{ $errors->first() }}</p>@endif
            <input name="mobile" value="{{ old('mobile') }}" placeholder="شماره موبایل" class="w-full border rounded-lg px-3 py-2" required>
            <input name="password" type="password" placeholder="رمز عبور" class="w-full border rounded-lg px-3 py-2" required>
            <button class="w-full bg-teal-700 text-white rounded-lg py-2">ورود</button>
            <a href="{{ route('register') }}" class="block text-center text-sm text-teal-700">ساخت کلینیک</a>
        </form>
    </div>
</x-guest-layout>
