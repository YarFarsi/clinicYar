<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-6">
        <form method="post" action="{{ route('register') }}" class="w-full max-w-lg bg-white rounded-2xl border border-slate-100 p-6 space-y-3">
            @csrf
            <h1 class="text-xl font-semibold">ساخت کلینیک</h1>
            <p class="text-sm text-slate-500">نوبت‌ها، بیماران، پرونده، پرداخت و گزارش‌های کلینیک در یکجا</p>
            @if($errors->any())<p class="text-sm text-rose-600">{{ $errors->first() }}</p>@endif
            <div class="grid grid-cols-2 gap-3">
                <input name="name" placeholder="نام شما" class="border rounded-lg px-3 py-2" required>
                <input name="mobile" placeholder="شماره موبایل" class="border rounded-lg px-3 py-2" required>
                <input name="password" type="password" placeholder="رمز عبور" class="border rounded-lg px-3 py-2" required>
                <input name="password_confirmation" type="password" placeholder="تکرار رمز" class="border rounded-lg px-3 py-2" required>
            </div>
            <input name="clinic_name" placeholder="نام کلینیک" class="w-full border rounded-lg px-3 py-2" required>
            <input name="clinic_phone" placeholder="شماره تماس کلینیک" class="w-full border rounded-lg px-3 py-2">
            <input name="address" placeholder="آدرس" class="w-full border rounded-lg px-3 py-2">
            <select name="type" class="w-full border rounded-lg px-3 py-2">
                @foreach(\App\Enums\ClinicType::cases() as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </select>
            <button class="w-full bg-teal-700 text-white rounded-lg py-2">ساخت کلینیک</button>
        </form>
    </div>
</x-guest-layout>
