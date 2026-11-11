import './bootstrap';
import Chart from 'chart.js/auto';
import { Html5Qrcode, Html5QrcodeScanner, Html5QrcodeSupportedFormats } from 'html5-qrcode';
import { createIcons, icons } from 'lucide';

window.Chart = Chart;
window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeScanner = Html5QrcodeScanner;
window.Html5QrcodeSupportedFormats = Html5QrcodeSupportedFormats;
window.createLucideIcons = () => createIcons({ icons });

// Re-init lucide icons on DOM ready and after Livewire morph/nav
document.addEventListener('DOMContentLoaded', () => {
    window.createLucideIcons();
});

document.addEventListener('livewire:navigated', () => {
    window.createLucideIcons();
});

if (window.Livewire) {
    window.Livewire.hook('morph.updated', () => {
        window.createLucideIcons();
    });
}
