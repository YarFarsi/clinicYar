# 🇮🇷 کلینیک‌یار

<p align="right">
  <a href="./README.md">🇮🇷 فارسی</a>
  &nbsp;|&nbsp;
  <a href="./README.en.md">🇬🇧 English</a>
</p>

**کلینیکت را ساده‌تر مدیریت کن.**

نرم‌افزار متن‌باز مدیریت کلینیک و مطب برای نوبت‌دهی، پرونده بیمار، پرداخت و گزارش. هسته سیستم روی شبکه داخلی کار می‌کند؛ حتی اگر اینترنت قطع شود، کار کلینیک متوقف نمی‌شود.

این سامانه تشخیص پزشکی، درمان یا نسخه معتبر تولید نمی‌کند.

---

## حمایت از خیریه

اگر کلینیک‌یار برایتان مفید است، می‌توانید از این دو مجموعه حمایت کنید:

- [خانه کودکان اِب](https://www.ebhome.ngo/support)
- [مؤسسه خیریه کسا](https://kassa-charity.org/donate/e-payment/)

---

## امکانات

- نوبت‌دهی با جلوگیری از تداخل زمان
- تقویم روزانه / هفتگی / ماهانه و صف انتظار
- CRM بیماران و پرونده مراجعه (عمومی، غیرتشخیصی)
- پرداخت، بدهی بیمار، هزینه و سود کلینیک
- گزارش عملکرد پزشک و خدمت
- چندکلینیکی (هر کلینیک داده جدا دارد)
- حالت فقط‌محلی و صف همگام‌سازی اختیاری
- پشتیبان‌گیری، نوبت آنلاین عمومی، PWA ساده

کد برنامه در پوشه [`src`](src) است. معماری: [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)

---

## اجرا با Docker

نیازمندی‌ها: Docker، Docker Compose، و برای فرانت‌اند Node.js.

```bash
cd src
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
npm install && npm run build
```

باز کنید: [http://localhost:8080](http://localhost:8080)

**دمو:** موبایل `09120000000` — رمز `password`

نصب بدون اینترنت: [`docs/AIRGAPPED_INSTALL.md`](docs/AIRGAPPED_INSTALL.md)  
عملیات آفلاین: [`docs/OFFLINE_OPERATION.md`](docs/OFFLINE_OPERATION.md)

---

## تست

```bash
cd src
docker run --rm -v "${PWD}:/app" -w /app composer:2 php artisan test
```

---

## مجوز

کد تحت مجوز Laravel / MIT اسکلت پروژه است. استفاده بالینی باید مطابق قوانین محلی و مسئولیت پزشک باشد.
