const banner = document.getElementById('offline-banner');

function updateStatus() {
    if (!navigator.onLine) {
        banner?.classList.remove('hidden');
        window.__uvox_offline = true;
    } else {
        banner?.classList.add('hidden');
        window.__uvox_offline = false;
    }
}

window.addEventListener('online', updateStatus);
window.addEventListener('offline', updateStatus);
updateStatus();
