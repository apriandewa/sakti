<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Judul halaman --}}
            <div class="mb-4 mt-2 text-center text-gray-600">
                <p>
                    Selamat datang di Website Resmi <br>
                    <strong>
                        <h2>{{ config('master.app.profile.name') }}</h2>
                    </strong>
                </p>
            </div>

            {{-- Input Email --}}
            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full"
                         type="email" name="email" :value="old('email')"
                         required autofocus autocomplete="username" />
            </div>

            {{-- Input Password + Toggle --}}
            <div class="mt-4 relative">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full pr-10"
                         type="password" name="password" required autocomplete="current-password" />

                <svg id="togglePassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                     class="toggle-password absolute right-3 top-9 w-5 h-5 cursor-pointer text-gray-500 transition-colors">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            {{-- Remember Me + Lupa Password --}}
            <div class="flex justify-between items-center mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-blue-400 hover:text-blue-600 transition">
                        {{ __('Lupa Password?') }}
                    </a>
                @endif
            </div>

             <!-- captcha -->
                <x-captcha />
                


            {{-- Tombol Aksi --}}
            <div class="flex justify-center space-x-4 mt-6">
                <!-- Tombol Kembali -->
                <a href="{{ url('/') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition ease-in-out duration-150">
                    <!-- Ikon panah kiri -->
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('Kembali') }}
                </a>
                <x-button>
                    {{ __('Masuk') }}
                </x-button>
            </div>
        </form>

            {{-- Divider --}}
            <div class="flex items-center my-6">
                <hr class="flex-grow border-t border-gray-300">
                <span class="mx-4 text-gray-400 text-sm">atau</span>
                <hr class="flex-grow border-t border-gray-300">
            </div>
            

        {{-- Login dengan Google --}}
        <div class="flex justify-end text-center items-center mt-3">
            <span class="mr-2 text-sm text-gray-600">Masuk menggunakan akun :</span>
            <a href="{{ route('google.login') }}"
            class="inline-flex items-center justify-center w-10 h-10 bg-red-500 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 transition ease-in-out duration-150">
            <!-- Ikon Google -->
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.35 11.1h-9.17v2.98h5.25c-.22 1.18-1.32 3.47-5.25 3.47-3.16 0-5.74-2.62-5.74-5.85s2.58-5.85 5.74-5.85c1.8 0 3.01.77 3.7 1.43l2.53-2.46C16.48 3.94 14.54 3 12.18 3 6.99 3 2.82 7.16 2.82 12.01s4.17 9.01 9.36 9.01c5.39 0 8.96-3.78 8.96-9.11 0-.61-.07-1.21-.19-1.81z"/>
            </svg>
            </a>
            <a href=""
            class="inline-flex items-center justify-center w-10 h-10 bg-purple-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-400 transition ease-in-out duration-150 ml-2">
            <!-- Ikon Kompas -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                <polygon points="12,7 15,17 12,15 9,17" fill="currentColor" />
            </svg>
            </a>
        </div>

    </x-authentication-card>
</x-guest-layout>
