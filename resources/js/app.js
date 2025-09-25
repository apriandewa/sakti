import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    // jika elemen tidak ada di halaman (mis. halaman lain), keluar aman
    if (!toggle || !password) return;

    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    toggle.addEventListener('click', () => {
        const isHidden = password.type === 'password';
        // toggle tipe input
        password.type = isHidden ? 'text' : 'password';

        // set class active saat password sedang ditampilkan (isHidden true -> akan jadi text)
        toggle.classList.toggle('active', isHidden);

        // update ikon jika kedua ikon ada
        if (eyeOpen && eyeClosed) {
            // ketika isHidden === true (sebelum klik input masih password) kita akan menampilkan -> eyeOpen muncul
            eyeClosed.classList.toggle('opacity-0', isHidden); // sembunyikan eyeClosed saat show
            eyeOpen.classList.toggle('opacity-0', !isHidden);   // tampilkan eyeOpen saat show
        } else {
            // fallback: kalau hanya satu ikon, toggle class agar CSS bisa merespon
            toggle.classList.toggle('eye-open', isHidden);
        }
    });
});
