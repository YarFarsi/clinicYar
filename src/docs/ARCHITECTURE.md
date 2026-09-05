# ClinicCRM Architecture

**Product:** نرم‌افزار مدیریت کلینیک و مطب  
**Positioning:** نوبت‌دهی بدون دردسر · پرونده و CRM بیمار · مدیریت درآمد و هزینه  
**Advantage:** حتی با قطع اینترنت، کار کلینیک متوقف نمی‌شود.

Medical disclaimer: ClinicCRM does **not** diagnose, treat, prescribe, or make clinical decisions. AI (if enabled) is non-diagnostic administrative assistance only.

---

## 1. Architecture diagram

```
Browser (PWA / Blade+Alpine, RTL)
        │
        ▼
Nginx (local LAN: 192.168.1.10)
        │
        ▼
Laravel  (PHP 8.3+, Modular Monolith)
        │
        ├── HTTP Controllers (thin)
        ├── Policies / Middleware / Scopes  ← tenant isolation
        ├── Application Services            ← business logic
        ├── Domain Events + Listeners
        ├── MySQL 8                         ← source of truth
        ├── Local storage/app/private       ← medical files
        └── Queue (database/redis)          ← sync, reminders, backups
                    │
                    ▼  (only if CONNECTED mode)
            External integrations
            WordPress / SMS / Email / AI
```

**Modes**

| Mode | Behavior |
|------|----------|
| `LOCAL_ONLY` | No outbound HTTP. Core CRUD, calendar, payments, reports, backup work on LAN. |
| `CONNECTED` | Same core + optional WordPress, SMS, email, AI, sync drain. |

Core never blocks on integrations. Failed outbound work lands in `sync_queue`.

---

## 2. ERD (logical)

```
clinics 1──* clinic_user *──1 users
clinics 1──* patients 1──1 patient_medical_profiles
clinics 1──* doctors ──? users
clinics 1──* services
clinics 1──* appointments
        appointments *──1 patients
        appointments *──1 doctors
        appointments *──? services
clinics 1──* medical_records (*──1 patients, *──? appointments)
clinics 1──* payments
clinics 1──* expenses *──1 expense_categories
clinics 1──* patient_follow_ups
clinics 1──* doctor_working_hours
clinics 1──* doctor_unavailable_times
clinics 1──* notifications
clinics 1──* sync_queue / sync_conflicts
clinics 1──* audit_logs
clinics 1──* imports / backups / integrations
```

Every clinic-owned table has `clinic_id`. Cross-tenant reads are forbidden by global scope + policies + tests.

---

## 3. Database schema (conventions)

- Money: `DECIMAL(18,2)` in **IRT** (تومان) stored as decimal, never float. Clinic `currency` default `IRT`.
- Dates: `appointment_date` DATE, `start_time`/`end_time` TIME; timestamps UTC in DB, displayed in clinic timezone (`Asia/Tehran` default).
- Soft deletes: patients, appointments (cancel is status, not delete).
- Encrypted at rest: `patient_medical_profiles` sensitive columns, integration credentials (`encrypted` cast).

See `docs/DATABASE.md` for full column list.

---

## 4. Migration list

Ordered (see `database/migrations/`):

1. users, password_reset, sessions, cache, jobs  
2. clinics  
3. roles, permissions, role_permission, clinic_user  
4. patients, patient_medical_profiles  
5. doctors, doctor_working_hours, doctor_unavailable_times  
6. services  
7. appointments  
8. medical_records  
9. expense_categories, expenses, payments  
10. patient_follow_ups  
11. notifications, audit_logs  
12. sync_queue, sync_conflicts  
13. imports, backups, integrations  

---

## 5–6. Models and relationships

| Model | Belongs to | Has |
|-------|------------|-----|
| Clinic | — | users (pivot), patients, doctors, services, appointments, … |
| User | clinics (many) | doctor profile (optional) |
| Patient | clinic | appointments, payments, records, follow-ups, medical profile |
| Doctor | clinic, user? | working hours, unavailable times, appointments |
| Appointment | clinic, doctor, patient, service?, createdBy | payments, medical records |
| Payment | clinic, patient, appointment?, createdBy | — |
| Expense | clinic, category, createdBy | — |

Trait `BelongsToClinic` applies `clinic_id` global scope from current tenant.

---

## 7. Permission matrix

| Permission | Owner | Admin | Doctor | Receptionist | Accountant |
|------------|:-----:|:-----:|:------:|:------------:|:----------:|
| patients.view/create/edit | ✓ | ✓ | ✓* | ✓ | ✓ view |
| patients.delete | ✓ | ✓ | | | |
| appointments.view/create/edit/cancel | ✓ | ✓ | ✓* | ✓ | view |
| doctors.view | ✓ | ✓ | ✓ | ✓ | ✓ |
| doctors.manage / staff.manage / users.manage | ✓ | ✓ | | | |
| medical_records.view/create/edit | ✓ | ✓ | ✓* | | |
| payments.view/create/edit | ✓ | ✓ | view | ✓ | ✓ |
| expenses.view/create/edit | ✓ | ✓ | | | ✓ |
| reports.view | ✓ | ✓ | ✓ own | | ✓ |
| settings.manage / integrations.manage | ✓ | ✓ | | | |

\* Doctor: only own patients/appointments/records unless Owner grants clinic-wide.

---

## 8. Route map

**Public:** `/` landing, `/login`, `/register`, `/book/{slug}`, `/install`  
**Auth + tenant:** `/dashboard`, `/appointments`, `/calendar`, `/waiting-room`, `/patients`, `/doctors`, `/services`, `/records`, `/payments`, `/expenses`, `/reports`, `/follow-ups`, `/integrations`, `/settings`, `/settings/backup`, `/settings/users`  
**API `/api/v1`:** auth, dashboard, patients, appointments, doctors, services, payments, expenses, follow-ups, reports, integrations, sync  

Envelope: `{ success, data, message, errors }`

---

## 9. Folder structure

```
app/Domain/          # Enums, value objects
app/Http/            # Controllers, Middleware, Requests
app/Models/
app/Policies/
app/Services/        # AppointmentService, AvailabilityService, …
app/Events/ Listeners/
app/Integrations/    # WordPress, AI providers
resources/views/     # Blade layouts + pages
resources/js/        # Alpine, PWA, offline banner
docs/                # Architecture, AIRGAPPED, OFFLINE
```

---

## 10. Pages (v1)

Landing, Login, Register, Onboarding wizard, Dashboard, Appointments, Calendar (day/week/month), New Appointment modal, Waiting Room, Patients, Patient Profile + Timeline, Medical Record, Doctors, Doctor Profile, Working Hours, Services, Payments, Expenses, Reports, Follow-ups, Integrations, Backup, Settings, Users, Roles, Public Booking, Installer.

---

## 11. UI components

`x-app-layout`, `x-sidebar`, `x-topbar`, `x-stat-card`, `x-modal`, `x-table`, `x-badge-status`, `x-timeline`, `x-offline-banner`, `x-empty-state`, appointment modal, quick-patient modal.

Design: professional medical, teal/slate, RTL, desktop-first receptionist, mobile-friendly doctor.

---

## 12. Offline architecture

LAN-first: all clients talk to clinic server. No CDN, no Google Fonts, no analytics.

- Service Worker caches app shell + static assets; offline fallback page.  
- Connectivity probe to `/up` (local) vs `/connectivity/external` (optional).  
- Banner: Online / Offline + pending sync count.  
- If WAN is down but LAN/MySQL is up, **all P0 features work**.

---

## 13. Sync architecture

`SyncService` writes `sync_queue` rows on domain events when `CONNECTED` and an integration is configured.

Statuses: pending → processing → completed | failed. Exponential backoff via `available_at`.

Conflicts stored in `sync_conflicts` for receptionist review. Core data is never overwritten blindly by remote.

---

## 14. Security model

- CSRF, hashed passwords, secure cookies, mass-assignment `$fillable`.  
- Tenant middleware binds clinic; global scopes; policies on every model.  
- Rate limit public booking + login.  
- Medical files under `storage/app/private/clinics/{id}/patients/{id}/medical/` streamed after auth.  
- No national ID / medical text in URLs, logs, or analytics.  
- Encrypted medical profile + integration secrets.  
- Audit log for create/cancel appointment, medical edit, payment, expense, backup restore, permission change.

---

## 15. Service list

AppointmentService, AvailabilityService, PatientService, MedicalRecordService, PaymentService, AccountingService, ReportService, FollowUpService, BackupService, ImportService, SyncService, WordPressService, OnboardingService, SearchService.

---

## 16. 8-week plan

| Week | Focus |
|------|--------|
| 1 | Setup, auth, tenancy, roles, installer, Docker |
| 2 | Patients, doctors, services, working hours |
| 3 | Appointments, availability, calendar, waiting room |
| 4 | Medical records, payments, expenses, dashboard |
| 5 | Reports, follow-ups, backup, CSV |
| 6 | Offline/PWA, sync queue, public booking |
| 7 | WordPress stub, AI adapter, security tests |
| 8 | Demo data polish, RTL QA, air-gapped docs |

**MVP implemented in this repository covers P0 + selected P1** so the demo flow (landing → clinic → appointment → waiting room → record → payment → dashboard → backup) is executable.
