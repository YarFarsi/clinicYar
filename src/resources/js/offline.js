function setOnline(online) {
    window.dispatchEvent(new CustomEvent('clinic-connectivity', { detail: { online } }));
    const banner = document.getElementById('offline-banner');
    if (banner) {
        banner.classList.toggle('hidden', online);
    }
    const dot = document.getElementById('online-dot');
    const label = document.getElementById('online-label');
    if (dot && label) {
        dot.className = online ? 'inline-block h-2 w-2 rounded-full bg-emerald-500' : 'inline-block h-2 w-2 rounded-full bg-amber-500';
        label.textContent = online ? 'آنلاین' : 'آفلاین';
    }
}

window.addEventListener('online', () => setOnline(true));
window.addEventListener('offline', () => setOnline(false));
setOnline(navigator.onLine);

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
