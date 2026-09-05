<x-guest-layout>
    <div class="min-h-screen">
        <header class="flex items-center justify-between px-8 py-5">
            <div class="font-semibold text-teal-800">کلینیک‌یار</div>
            <div class="flex gap-3 text-sm">
                <a href="{{ route('login') }}" class="px-4 py-2">ورود</a>
                <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-teal-700 text-white">ساخت کلینیک رایگان</a>
            </div>
        </header>
        <section class="max-w-4xl mx-auto px-6 pt-16 pb-10 text-center">
            <p class="text-sm text-teal-700 mb-3">نرم‌افزار مدیریت کلینیک و مطب</p>
            <h1 class="text-4xl font-bold text-slate-900 leading-tight">کلینیکت را ساده‌تر مدیریت کن.</h1>
            <p class="mt-4 text-lg text-slate-600">نوبت‌دهی، پرونده بیمار، پرداخت و گزارش در یک سیستم.</p>
            <p class="mt-2 text-slate-500">حتی با قطع اینترنت، کار کلینیک متوقف نمی‌شود.</p>
            <div class="mt-8 flex justify-center gap-3">
                <a href="{{ route('register') }}" class="rounded-lg bg-teal-700 text-white px-6 py-3">ساخت کلینیک رایگان</a>
                <a href="{{ route('login') }}" class="rounded-lg border border-slate-200 px-6 py-3">مشاهده Demo</a>
            </div>
            <p class="mt-3 text-xs text-slate-400">دمو: موبایل ۰۹۱۲۰۰۰۰۰۰۰ رمز password</p>
        </section>
        <section class="max-w-5xl mx-auto grid sm:grid-cols-2 lg:grid-cols-4 gap-4 px-6 pb-20">
            @foreach([
                ['نوبت‌دهی', 'جلوگیری از تداخل نوبت و تقویم روزانه'],
                ['CRM بیماران', 'پرونده، تاریخچه و پیگیری در یکجا'],
                ['پرداخت و بدهی', 'درآمد، هزینه و سود کلینیک'],
                ['آفلاین', 'ادامه کار روی شبکه داخلی بدون اینترنت'],
                ['Backup', 'پشتیبان‌گیری از داده‌های پزشکی'],
                ['گزارش', 'عملکرد پزشک، خدمت و مراجعه'],
                ['صف انتظار', 'وضعیت حضور بیمار برای منشی'],
                ['نوبت آنلاین', 'صفحه عمومی رزرو برای سایت کلینیک'],
            ] as [$t,$d])
                <div class="rounded-xl bg-white border border-slate-100 p-4">
                    <div class="font-medium">{{ $t }}</div>
                    <div class="text-sm text-slate-500 mt-1">{{ $d }}</div>
                </div>
            @endforeach
        </section>
        <footer class="text-center text-xs text-slate-500 pb-10 px-6 space-y-2">
            <p>این سامانه تشخیص یا درمان پزشکی انجام نمی‌دهد و جایگزین تصمیم پزشک نیست.</p>
            <p>
                حمایت از خیریه:
                <a class="text-teal-700" href="https://www.ebhome.ngo/support" rel="noopener noreferrer" target="_blank">خانه کودکان اِب</a>
                ·
                <a class="text-teal-700" href="https://kassa-charity.org/donate/e-payment/" rel="noopener noreferrer" target="_blank">مؤسسه خیریه کسا</a>
            </p>
        </footer>
    </div>
</x-guest-layout>
