import 'altcha';
import QRCode from 'qrcode';

function renderShareQrCodes() {
    document.querySelectorAll('[data-qrcode-url]').forEach((canvas) => {
        QRCode.toCanvas(canvas, canvas.dataset.qrcodeUrl, { width: 160, margin: 1 });
    });
}

document.addEventListener('DOMContentLoaded', renderShareQrCodes);
document.addEventListener('livewire:navigated', renderShareQrCodes);
