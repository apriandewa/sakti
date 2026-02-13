import './bootstrap';
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (!toggle || !password) return;

    toggle.addEventListener('click', () => {
        const isPassword = password.type === 'password';
        password.type = isPassword ? 'text' : 'password';

        // Update icon SVG
        toggle.innerHTML = isPassword
            ? `
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.98 8.223A10.477 10.477 0 001.5 12c3.75 6.75 9.75 6.75 9.75 6.75 1.82 0 3.55-.39 5.14-1.11M21 21L3 3" />
              `
            : `
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              `;

        // Opsional: ubah warna saat aktif
        toggle.classList.toggle('text-blue-500', isPassword);
    });
});
