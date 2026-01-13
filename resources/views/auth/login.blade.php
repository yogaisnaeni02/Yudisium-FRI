<x-guest-layout>
    @section('title', 'Masuk')
    
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
        <p class="text-gray-600">Masuk ke akun SIYU Anda</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
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
                placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold mb-2" />
            <x-text-input id="password" 
                class="block mt-1 w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg"
                type="password"
                name="password"
                required 
                autocomplete="current-password"
                placeholder="••••••••" />
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
            {{ __('Masuk') }}
        </button>

        <!-- Info -->
        <div class="text-center text-sm text-gray-600">
            Hubungi admin untuk mendapatkan akun
        </div>
    </form>
</x-guest-layout>
