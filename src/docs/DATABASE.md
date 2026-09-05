# Complete database schema

Convention: money `DECIMAL(18,2)` IRT (تومان). Datetimes stored UTC. Soft-deletes on patients.

## clinics
id, name, slug unique, phone, email, address, city, timezone default Asia/Tehran, currency default IRT, logo, type enum (general,dental,beauty,physiotherapy,psychology,medical,other), operation_mode enum (local_only, connected) default local_only, settings json, created_at, updated_at

## users
id, name, mobile unique, email nullable unique, password, remember_token, timestamps

## clinic_user
clinic_id, user_id, role (owner,admin,doctor,receptionist,accountant), unique(clinic_id,user_id)

## permissions
id, name unique (patients.view, …)

## role_permission
role, permission_id

## patients
id, clinic_id, first_name, last_name, national_id nullable, phone nullable, mobile, email, birth_date, gender enum, address, emergency_contact, notes, timestamps, deleted_at
INDEX(clinic_id, mobile), INDEX(clinic_id, last_name), INDEX(clinic_id, phone)

## patient_medical_profiles
id, clinic_id, patient_id unique, allergies encrypted, medications encrypted, medical_conditions encrypted, past_surgeries encrypted, family_history encrypted, notes encrypted, timestamps

## doctors
id, clinic_id, user_id nullable, first_name, last_name, specialty, license_number, bio, active boolean, timestamps

## doctor_working_hours
id, clinic_id, doctor_id, day_of_week tinyint 0-6 (Sat=6 to match Carbon iso? We use 0=Saturday Persian week internally as 6=Saturday ISO — stored as Carbon dayOfWeek: 0 Sunday … 6 Saturday. UI maps Persian شنبه=Saturday), start_time, end_time, slot_duration unsigned, active

## doctor_unavailable_times
id, clinic_id, doctor_id, start_at, end_at, reason, timestamps

## services
id, clinic_id, name, category, duration_minutes, price decimal(18,2), description, active, timestamps

## appointments
id, clinic_id, doctor_id, patient_id, service_id nullable, appointment_date date, start_time time, end_time time, status, source, notes, created_by, timestamps
status: scheduled,confirmed,arrived,in_progress,completed,cancelled,no_show
source: manual,website,phone,online,integration
INDEX(clinic_id, appointment_date), INDEX(clinic_id, doctor_id, appointment_date), INDEX(clinic_id, patient_id)
Unique overlap prevented in transaction via `lockForUpdate` on doctor+date rows, not a naive unique on start_time (durations vary).

## medical_records
id, clinic_id, patient_id, doctor_id, appointment_id nullable, chief_complaint, diagnosis, notes, treatment, follow_up_notes, timestamps

## payments
id, clinic_id, patient_id, appointment_id nullable, amount decimal(18,2), payment_method (cash,card,transfer,online,other), reference, paid_at, notes, created_by, timestamps
INDEX(clinic_id, patient_id)

## expense_categories
id, clinic_id nullable (null = system default), name, slug

## expenses
id, clinic_id, category_id, amount decimal(18,2), description, expense_date, payment_method, created_by, timestamps
INDEX(clinic_id, expense_date)

## patient_follow_ups
id, clinic_id, patient_id, doctor_id nullable, appointment_id nullable, title, description, due_at, status (pending,done,cancelled), created_by, completed_at, timestamps
INDEX(clinic_id, due_at)

## notifications
id, clinic_id, user_id, type, title, body, read_at, timestamps

## sync_queue
id, clinic_id, user_id nullable, entity_type, entity_id, action, payload json, status (pending,processing,completed,failed), attempts, last_error, available_at, processed_at, timestamps

## sync_conflicts
id, clinic_id, sync_queue_id, entity_type, entity_id, local_payload, remote_payload, resolution, timestamps

## audit_logs
id, clinic_id, user_id nullable, action, entity_type, entity_id, old_values json, new_values json, ip_address, user_agent, created_at

## imports
id, clinic_id, user_id, type, filename, mapping json, status, result json, timestamps

## backups
id, clinic_id, user_id, path, size, checksum, verified_at, timestamps

## integrations
id, clinic_id, provider, enabled, credentials encrypted, settings json, timestamps
