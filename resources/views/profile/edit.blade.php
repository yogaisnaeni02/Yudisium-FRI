@extends('layouts.dashboard')

@section('title', 'Profile')
@section('page-title', 'Pengaturan Profile')

@section('breadcrumb')
    @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    @else
        <a href="{{ route('student.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    @endif
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Profile</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Success Message -->
    @if (session('status') === 'profile-updated')
        <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-lg" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-green-700 font-medium">Profile berhasil diperbarui!</p>
            </div>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-lg" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-green-700 font-medium">Password berhasil diperbarui!</p>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-lg" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-10 gap-6">
        <div class="col-span-10 md:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-green-200 overflow-hidden h-full">
                <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Foto Profile
                    </h3>
                </div>
                <div class="p-6">
                    <div class="photo-profile-wrapper">
                        @include('profile.partials.update-profile-photo')
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-10 md:col-span-7">
            <div class="bg-white rounded-xl shadow-sm border border-green-200 overflow-hidden h-full">
                <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                            </path>
                        </svg>
                        Informasi Profile
                    </h3>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

    </div>

    <!-- Update Password Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                Ubah Password
            </h3>
        </div>
        <div class="p-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>
</div>
@endsection
