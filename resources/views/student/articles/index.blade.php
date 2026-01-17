@extends('layouts.dashboard')

@section('title', 'Informasi Yudisium')
@section('page-title', 'Informasi Yudisium')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Informasi Yudisium</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-lg p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">Informasi Yudisium</h2>
        <p class="text-green-100">Informasi dan panduan terkait yudisium</p>
    </div>

    <!-- Articles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($articles as $article)
            <a href="{{ route('student.article-detail', $article) }}" class="bg-white rounded-xl shadow-sm border border-green-200 overflow-hidden hover:shadow-md transition group">
                @if($article->image)
                <div class="h-48 bg-gray-200 overflow-hidden">
                    <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                @else
                <div class="h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                @endif
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-gray-500">{{ $article->published_at->format('d M Y') }}</span>
                        <span class="text-xs text-gray-500">{{ $article->views }} views</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-green-600 transition">{{ $article->title }}</h3>
                    @if($article->excerpt)
                    <p class="text-sm text-gray-600 line-clamp-3">{{ $article->excerpt }}</p>
                    @else
                    <p class="text-sm text-gray-600 line-clamp-3">{{ Str::limit(strip_tags($article->content), 150) }}</p>
                    @endif
                    <div class="mt-4 flex items-center text-green-600 text-sm font-medium">
                        Baca selengkapnya
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-500">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="font-medium">Belum ada informasi</p>
                <p class="text-sm mt-2">Informasi akan muncul di sini setelah dipublikasikan oleh admin</p>
            </div>
        @endforelse
    </div>

    @if($articles->hasPages())
    <div class="bg-white rounded-xl shadow-sm border border-green-200 p-4">
        {{ $articles->links() }}
    </div>
    @endif
</div>
@endsection

