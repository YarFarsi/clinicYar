# 🇬🇧 ClinicYar (کلینیک‌یار)

<p>
  <a href="./README.md">🇮🇷 فارسی</a>
  &nbsp;|&nbsp;
  <a href="./README.en.md">🇬🇧 English</a>
</p>

**Run your clinic with less friction.**

Open-source clinic and practice software for appointments, patient records, payments, and reports. The core runs on your local network, so clinic work continues even when the public internet is down.

ClinicYar does **not** diagnose, treat, prescribe, or make clinical decisions.

---

## Support charities

If ClinicYar is useful to you, please consider supporting:

- [EB Home (خانه کودکان اِب)](https://www.ebhome.ngo/support)
- [Kassa Charity (مؤسسه خیریه کسا)](https://kassa-charity.org/donate/e-payment/)

---

## Features

- Appointment booking with double-booking prevention
- Day / week / month calendar and waiting room
- Patient CRM and visit notes (non-diagnostic)
- Payments, patient balance, expenses, and net income
- Doctor and service reports
- Multi-tenant clinics (strict data isolation)
- Local-only mode and optional sync queue
- Backup, public online booking, simple PWA

Application code lives in [`src`](src). Architecture: [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)

---

## Run with Docker

Requires Docker, Docker Compose, and Node.js for the frontend build.

```bash
cd src
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
npm install && npm run build
```

Open [http://localhost:8080](http://localhost:8080)

**Demo:** mobile `09120000000` — password `password`

Air-gapped install: [`docs/AIRGAPPED_INSTALL.md`](docs/AIRGAPPED_INSTALL.md)  
Offline behavior: [`docs/OFFLINE_OPERATION.md`](docs/OFFLINE_OPERATION.md)

---

## Tests

```bash
cd src
docker run --rm -v "${PWD}:/app" -w /app composer:2 php artisan test
```

---

## License

The Laravel skeleton is MIT-licensed. Clinical use remains the practitioner’s responsibility under local regulations.
