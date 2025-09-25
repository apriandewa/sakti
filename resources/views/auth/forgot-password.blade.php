<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 mt-4 text-sm text-gray-600">
            {{ __('Lupa kata sandi? Tidak masalah. Cukup beri tahu kami alamat email Anda, dan kami akan mengirimkan tautan pengaturan ulang kata sandi melalui email agar Anda dapat memilih kata sandi baru.') }}
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <!-- captcha -->
                <x-captcha />
                
            <div class="flex items-center justify-between mt-4">
                <!-- Tombol Kembali -->
                <a href="{{ route('login') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition ease-in-out duration-150">
                    <!-- Ikon panah kiri -->
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('Kembali') }}
                </a>

                <!-- Tombol Reset Password -->
                <x-button class="inline-flex items-center">
                    <!-- Ikon pesawat (send) -->
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 12l14-7-7 14-2-5-5-2z" />
                    </svg>
                    {{ __('Reset Password') }}
                </x-button>
            </div>

        </form>
    </x-authentication-card>
</x-guest-layout>
