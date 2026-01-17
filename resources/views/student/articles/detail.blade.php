@extends('layouts.dashboard')

@section('title', $article->title)
@section('page-title', $article->title)

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-green-400">/</span>
    <a href="{{ route('student.articles') }}" class="text-green-600 hover:text-green-700">Informasi Yudisium</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">{{ Str::limit($article->title, 30) }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($article->image)
        <div class="h-64 md:h-96 bg-green-200 overflow-hidden">
            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
        </div>
        @endif
        
        <div class="p-8">
            <!-- Article Meta -->
            <div class="flex items-center justify-between mb-6 text-sm text-green-700">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $article->published_at->format('d F Y') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ $article->views }} views
                    </span>
                </div>
                <span class="text-green-800 font-medium">Oleh: {{ $article->user->name }}</span>
            </div>

            <!-- Article Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $article->title }}</h1>

            <!-- Article Excerpt -->
            @if($article->excerpt)
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r">
                <p class="text-gray-700 italic">{{ $article->excerpt }}</p>
            </div>
            @endif

            <!-- Article Content -->
            <div class="prose max-w-none mb-8">
                <div class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $article->content }}</div>
            </div>

            <!-- Back Button -->
            <div class="pt-6 border-t border-green-200">
                <a href="{{ route('student.articles') }}" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar Informasi
                </a>
            </div>
        </div>
    </article>
</div>
@endsection

