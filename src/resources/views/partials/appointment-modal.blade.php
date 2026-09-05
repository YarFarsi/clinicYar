<div x-data="appointmentModal()" x-cloak>
    <button type="button" @click="open = true; load()" class="fixed bottom-6 left-6 z-40 rounded-full bg-teal-700 text-white px-5 py-3 shadow text-sm font-medium">+ نوبت جدید</button>
    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4" @keydown.escape.window="open=false">
        <div class="bg-white rounded-xl w-full max-w-lg p-5" @click.outside="open=false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">ثبت نوبت</h3>
                <button @click="open=false" class="text-slate-400">بستن</button>
            </div>
            <form method="post" action="{{ route('appointments.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs text-slate-500">بیمار</label>
                    <select name="patient_id" class="w-full border rounded-lg px-3 py-2 text-sm" x-model="patientId" required>
                        <option value="">انتخاب کنید</option>
                        <template x-for="p in patients" :key="p.id">
                            <option :value="p.id" x-text="p.first_name + ' ' + p.last_name + ' — ' + p.mobile"></option>
                        </template>
                    </select>
                    <button type="button" class="text-xs text-teal-700 mt-1" @click="quick=true">+ بیمار جدید</button>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-slate-500">پزشک</label>
                        <select name="doctor_id" class="w-full border rounded-lg px-3 py-2 text-sm" x-model="doctorId" @change="fetchSlots()" required>
                            <option value="">انتخاب</option>
                            <template x-for="d in doctors" :key="d.id">
                                <option :value="d.id" x-text="'دکتر ' + d.first_name + ' ' + d.last_name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">خدمت</label>
                        <select name="service_id" class="w-full border rounded-lg px-3 py-2 text-sm" x-model="serviceId" @change="fetchSlots()">
                            <option value="">—</option>
                            <template x-for="s in services" :key="s.id">
                                <option :value="s.id" x-text="s.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-slate-500">تاریخ</label>
                        <input type="date" name="appointment_date" class="w-full border rounded-lg px-3 py-2 text-sm" x-model="date" @change="fetchSlots()" required>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">ساعت</label>
                        <select name="start_time" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                            <option value="">انتخاب زمان آزاد</option>
                            <template x-for="t in slots" :key="t">
                                <option :value="t" x-text="t"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <textarea name="notes" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="یادداشت"></textarea>
                <button class="w-full bg-teal-700 text-white rounded-lg py-2 text-sm">ثبت نوبت</button>
            </form>
            <div x-show="quick" class="mt-4 border-t pt-4">
                <form @submit.prevent="savePatient" class="grid grid-cols-3 gap-2">
                    <input x-model="qf" placeholder="نام" class="border rounded px-2 py-1 text-sm" required>
                    <input x-model="ql" placeholder="نام خانوادگی" class="border rounded px-2 py-1 text-sm" required>
                    <input x-model="qm" placeholder="موبایل" class="border rounded px-2 py-1 text-sm" required>
                    <button class="col-span-3 text-sm bg-slate-800 text-white rounded py-1">ذخیره بیمار</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function appointmentModal() {
    return {
        open: false, quick: false, doctors: [], patients: [], services: [], slots: [],
        doctorId: '', patientId: '', serviceId: '', date: '{{ now()->toDateString() }}',
        qf: '', ql: '', qm: '',
        async load() {
            const r = await fetch('{{ route('appointments.form-data') }}', { headers: { 'Accept': 'application/json' }});
            const j = await r.json();
            this.doctors = j.data.doctors; this.patients = j.data.patients; this.services = j.data.services;
        },
        async fetchSlots() {
            if (!this.doctorId || !this.date) return;
            const u = new URL('{{ route('appointments.slots') }}', window.location.origin);
            u.searchParams.set('doctor_id', this.doctorId);
            u.searchParams.set('date', this.date);
            if (this.serviceId) u.searchParams.set('service_id', this.serviceId);
            const r = await fetch(u, { headers: { 'Accept': 'application/json' }});
            const j = await r.json();
            this.slots = j.data || [];
        },
        async savePatient() {
            const token = document.querySelector('meta[name=csrf-token]').content;
            const r = await fetch('{{ route('patients.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ first_name: this.qf, last_name: this.ql, mobile: this.qm })
            });
            const j = await r.json();
            if (j.success) {
                this.patients.unshift(j.data);
                this.patientId = j.data.id;
                this.quick = false;
            }
        }
    }
}
</script>
