# Air-gapped install

ClinicCRM هسته را روی LAN اجرا می‌کند و به CDN یا API ابری وابسته نیست.

## Build image (با اینترنت، یک‌بار)

```bash
docker compose build
docker save clinic-app:latest -o cliniccrm-app.tar
docker pull mysql:8.4
docker pull nginx:1.27-alpine
docker save mysql:8.4 nginx:1.27-alpine -o cliniccrm-base.tar
```

اگر نام image متفاوت است: `docker compose images`

## Transfer

فایل‌های tar و پوشه پروژه (بدون نیاز به vendor اگر داخل image باشد) را با USB به سرور کلینیک منتقل کنید.

## Import

```bash
docker load -i cliniccrm-base.tar
docker load -i cliniccrm-app.tar
```

## Compose up

```bash
cp .env.example .env
# APP_KEY را با php artisan key:generate داخل کانتینر بسازید
docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

## شبکه محلی

سرور: `192.168.1.10`  
منشی و پزشک مرورگر را به `http://192.168.1.10:8080` وصل کنند.  
اینترنت لازم نیست. DNS داخلی اختیاری است.

Installer را پس از نصب قفل می‌کند (`storage/app/installed.lock`).
