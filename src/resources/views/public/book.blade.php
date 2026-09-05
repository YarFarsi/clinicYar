<x-guest-layout>
<div class="min-h-screen p-6 max-w-lg mx-auto">
    <h1 class="text-xl font-semibold mb-1">رزرو نوبت — {{ $clinic->name }}</h1>
    <p class="text-sm text-slate-500 mb-4">اطلاعات پزشکی در این صفحه نمایش داده نمی‌شود.</p>
    @if(session('ok'))<div class="bg-emerald-50 text-emerald-800 p-3 rounded mb-3 text-sm">{{ session('ok') }}</div>@endif
    @if($errors->any())<div class="bg-rose-50 text-rose-800 p-3 rounded mb-3 text-sm">{{ $errors->first() }}</div>@endif
    <form method="post" class="bg-white border rounded-xl p-4 space-y-3" x-data="{slots:[]}" @change="
        const d=document.querySelector('[name=doctor_id]').value;
        const dt=document.querySelector('[name=appointment_date]').value;
        if(d&&dt){ fetch(`{{ url('/book/'.$clinic->slug.'/slots') }}?doctor_id=${d}&date=${dt}`).then(r=>r.json()).then(j=>slots=j.data||[]) }
    ">
        @csrf
        <select name="doctor_id" class="w-full border rounded px-3 py-2" required>
            <option value="">پزشک</option>
            @foreach($doctors as $d)<option value="{{ $d->id }}">{{ $d->fullName() }}</option>@endforeach
        </select>
        <select name="service_id" class="w-full border rounded px-3 py-2">
            <option value="">خدمت</option>
            @foreach($services as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
        </select>
        <input type="date" name="appointment_date" class="w-full border rounded px-3 py-2" required>
        <select name="start_time" class="w-full border rounded px-3 py-2" required>
            <option value="">ساعت آزاد</option>
            <template x-for="t in slots"><option :value="t" x-text="t"></option></template>
        </select>
        <input name="first_name" placeholder="نام" class="w-full border rounded px-3 py-2" required>
        <input name="last_name" placeholder="نام خانوادگی" class="w-full border rounded px-3 py-2" required>
        <input name="mobile" placeholder="موبایل" class="w-full border rounded px-3 py-2" required>
        <button class="w-full bg-teal-700 text-white rounded py-2">ثبت درخواست</button>
    </form>
</div>
</x-guest-layout>
