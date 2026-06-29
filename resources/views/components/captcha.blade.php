<div id="captcha-component" class="mt-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Kode Keamanan (Captcha)
    </label>

    <div class="flex items-center gap-3">
        <!-- Container gambar captcha -->
        <span id="captcha-img" class="inline-block"></span>

        <!-- Tombol reload captcha -->
        <button type="button" id="reload-captcha"
                class="px-2 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300">
            ⟳
        </button>

        <!-- Hidden field untuk captcha_key -->
    <input type="hidden" name="captcha_key" id="captcha_key">

    <!-- Input user untuk captcha -->
    <input id="captcha" type="text" name="captcha"
           placeholder="Masukkan kode"
           class="mt-2 border rounded  px-2 py-1">

    </div>

    
    @error('captcha')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const captchaImg = document.getElementById('captcha-img');
    const captchaKey = document.getElementById('captcha_key');
    const reloadBtn = document.getElementById('reload-captcha');

    // Fungsi load captcha dari endpoint stateless
    async function loadCaptcha() {
        try {
            const res = await fetch('{{ url("/captcha/api") }}');
            if (!res.ok) throw new Error('Gagal load captcha');
            const data = await res.json();

            // Render gambar captcha dari Base64
            // PENTING: buat tag <img> agar browser menampilkan gambar
            captchaImg.innerHTML = `<img src="${data.img}" alt="Captcha" class="h-10 rounded border">`;

            // Simpan key untuk validasi backend
            captchaKey.value = data.key;
        } catch (err) {
            console.error('Error load captcha:', err);
        }
    }

    // Load pertama kali saat halaman siap
    loadCaptcha();

    // Reload captcha ketika tombol diklik
    reloadBtn.addEventListener('click', loadCaptcha);
});
</script>
@endpush
