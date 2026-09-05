# Offline operation

## همیشه بدون اینترنت کار می‌کند

Login، بیماران، نوبت، تقویم، صف انتظار، پرونده، پرداخت، هزینه، گزارش، جستجو، Backup محلی.

شرط: دسترسی LAN به سرور PHP/MySQL کلینیک.

## اینترنت لازم است (اختیاری)

- SMS / WhatsApp / Email یادآوری
- WordPress / نوبت از سایت
- AI خارجی
- Sync به سرویس ابری

اگر قطع شوند، هسته متوقف نمی‌شود. کار در `sync_queue` با وضعیت `pending` می‌ماند.

## Banner

آفلاین بودن WAN با `navigator.onLine` در مرورگر نشان داده می‌شود. این به معنی قطع سرور محلی نیست.

## Sync

Settings → حالت CONNECTED + Integration فعال.  
`POST /sync` صف را پردازش می‌کند. LOCAL_ONLY هیچ صف خروجی نمی‌سازد.
