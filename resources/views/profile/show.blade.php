@extends('backend.main.index')

@push('css')
    <!-- Include Tailwind CSS with Preflight Disabled to prevent conflict with Bootstrap -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            }
        }
    </script>

    @livewireStyles
    <style>
        /* Fix some basic tailwind form inputs if needed */
        [type='text'], [type='email'], [type='url'], [type='password'], [type='number'], [type='date'], [type='datetime-local'], [type='month'], [type='search'], [type='tel'], [type='time'], [type='week'], [multiple], textarea, select {
            border-color: #d1d5db;
            border-radius: 0.375rem;
            border-width: 1px;
            padding: 0.5rem 0.75rem;
        }
        .text-gray-800 { color: #1f2937; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-900 { color: #111827; }
        .bg-white { background-color: #ffffff; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
        .rounded-md { border-radius: 0.375rem; }
        /* Prevent Tailwind bg from overriding admin background if needed */
        .profile-container {
            font-family: 'Figtree', sans-serif;
            text-align: left;
        }
    </style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <h3 class="page-title">{{ __('Profile') }}</h3>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content profile-container">
            <div class="row">
                <div class="col-12">
                    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('profile.update-profile-information-form')
                            <x-section-border />
                        @endif

                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                            <div class="mt-10 sm:mt-0">
                                @livewire('profile.update-password-form')
                            </div>
                            <x-section-border />
                        @endif

                        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                            <div class="mt-10 sm:mt-0">
                                @livewire('profile.two-factor-authentication-form')
                            </div>
                            <x-section-border />
                        @endif

                        <div class="mt-10 sm:mt-0">
                            @livewire('profile.logout-other-browser-sessions-form')
                        </div>

                        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                            <x-section-border />
                            <div class="mt-10 sm:mt-0">
                                @livewire('profile.delete-user-form')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('js')
    @livewireScripts
@endpush
