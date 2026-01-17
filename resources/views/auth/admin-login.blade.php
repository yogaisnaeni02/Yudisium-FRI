<x-guest-layout>
    @section('title', 'Masuk Admin')
    
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
        <p class="text-gray-600">Masuk ke akun admin SIYU</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-5">
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold mb-2" />
            <x-text-input id="email" 
                class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="example@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <x-input-label for="adminPassword" :value="__('Password')" class="text-gray-700 font-semibold mb-2" />
            <div class="relative">
                <x-text-input id="adminPassword" 
                    class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg pr-10"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••" />
                <button 
                    type="button" 
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                    id="toggleAdminPassword"
                    onclick="toggleAdminPasswordVisibility()">
                    <svg id="eyeAdminIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" 
                    type="checkbox" 
                    class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" 
                    name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="text-sm text-green-600 hover:text-green-700 font-medium underline underline-offset-2" href="{{ route('password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition shadow-lg hover:shadow-xl mb-4">
            {{ __('Masuk Admin') }}
        </button>
    </form>

    <script>
        function toggleAdminPasswordVisibility() {
            const passwordInput = document.getElementById('adminPassword');
            const eyeIcon = document.getElementById('eyeAdminIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</x-admin-guest-layout>
