<div class="mt-4">
    <label for="captcha" class="block text-sm font-medium text-gray-700">Kode Keamanan</label>
    <div class="flex items-center gap-2 mt-1">
        <input id="captcha" type="text" name="captcha"
               class="border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">

        <div class="flex items-center gap-1">
            <img id="captcha-img" src="{{ captcha_src() }}" alt="captcha" class="h-10 rounded border">
            <button type="button" id="reload-captcha" class="px-2 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300">
                ⟳
            </button>
        </div>
    </div>

    @error('captcha')
        <span class="text-sm text-red-500">{{ $message }}</span>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('captcha-img').src = '{{ captcha_src() }}' + '?' + Math.random();
            });
        </script>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const reloadBtn = document.getElementById('reload-captcha');
    const captchaImg = document.getElementById('captcha-img');

    reloadBtn.addEventListener('click', () => {
        captchaImg.src = '{{ captcha_src() }}' + '?' + Math.random();
    });
});
</script>
@endpush
