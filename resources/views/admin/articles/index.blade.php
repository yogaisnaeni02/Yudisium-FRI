@extends('layouts.dashboard')

@section('title', 'Informasi Yudisium')
@section('page-title', 'Informasi Yudisium')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-green-600 hover:text-green-700">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900">Informasi Yudisium</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Create Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Informasi Yudisium</h2>
            <p class="text-gray-600 mt-1">Kelola informasi yudisium untuk mahasiswa</p>
        </div>
        <x-button variant="primary" href="{{ route('admin.articles.create') }}" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>'>
            Tambah Informasi Baru
        </x-button>
    </div>

    <!-- Articles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($articles as $article)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                @if($article->image)
                <div class="h-48 bg-gray-200 overflow-hidden">
                    <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
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
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $article->status === 'published' ? 'Published' : 'Draft' }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $article->views }} views</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $article->title }}</h3>
                    @if($article->excerpt)
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $article->excerpt }}</p>
                    @endif
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        <span>Oleh: {{ $article->user->name }}</span>
                        <span>{{ $article->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-button variant="info" size="sm" href="{{ route('admin.articles.edit', $article) }}" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'>
                            Edit
                        </x-button>
                        <form action="{{ route('admin.articles.delete', $article) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus informasi ini?')">
                            @csrf
                            @method('DELETE')
                            <x-button variant="danger" size="sm" type="submit" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'>
                                Hapus
                            </x-button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-500">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="font-medium">Belum ada informasi</p>
                <p class="text-sm mt-2">Mulai dengan membuat informasi baru</p>
            </div>
        @endforelse
    </div>

    @if($articles->hasPages())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        {{ $articles->links() }}
    </div>
    @endif
</div>
@endsection

