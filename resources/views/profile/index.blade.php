@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="bg-white min-h-screen pb-24"> {{-- Padding bawah agar tidak tertutup bottom nav --}}

    {{-- Header --}}
    <header class="px-6 pt-8 pb-4">
        <h3 class="text-2xl font-bold text-gray-900">Profile</h3>
    </header>

    {{-- User Info Section --}}
    <div class="flex flex-col items-center mt-4 mb-8">
        {{-- Avatar Circle --}}
        <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center text-[#37967D] text-4xl mb-4 ring-4 ring-green-50">
            {{-- Mengambil inisial nama depan --}}
            <span class="font-bold">{{ substr($user->name ?? 'U', 0, 1) }}</span>
        </div>

        {{-- Name & Email --}}
        <h2 class="text-xl font-bold text-gray-900">{{ $user->name ?? 'Nama Pengguna' }}</h2>
        <p class="text-sm text-gray-500">{{ $user->email ?? 'email@contoh.com' }}</p>

        {{-- Edit Button --}}
        <button type="button" class="mt-4 bg-[#37967D] text-white px-6 py-2 rounded-full text-xs font-bold tracking-wide hover:bg-[#2a7561] transition shadow-lg shadow-green-900/10 active:scale-95 transform">
            Edit Profile
        </button>
    </div>

    {{-- Menu List Helper Component --}}
    @php
    // Fungsi kecil untuk render item menu agar kodenya rapi
    function renderMenuItem($icon, $label, $link = '#') {
        return '
        <a href="'.$link.'"
            class="flex items-center justify-between px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition group cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="text-gray-500 w-6 text-center group-hover:text-[#37967D] transition">
                    <i class="ph-fill '.$icon.' text-xl"></i>
                </div>
                <span class="text-gray-700 font-medium text-[14px] group-hover:text-gray-900 transition">'.$label.'</span>
            </div>
            <i class="ph-bold ph-caret-right text-gray-300 text-sm group-hover:text-[#37967D] transition"></i>
        </a>
        ';
    }
    @endphp

    {{-- Section: Pengaturan Umum --}}
    <div class="mt-2">
        <h3 class="px-6 text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pengaturan Umum</h3>
        <div class="bg-white flex flex-col border-t border-gray-50">
            {{-- Icon diganti ke Phosphor Icons (ph-...) --}}
            {!! renderMenuItem('ph-storefront', 'Data Kantin', '#') !!}
            {!! renderMenuItem('ph-bell', 'Notifikasi', route('notifications.index')) !!}
            {!! renderMenuItem('ph-lock-key', 'Ganti Password', '#') !!}
            {!! renderMenuItem('ph-gear', 'Pengaturan', '#') !!}
        </div>
    </div>

    {{-- Section: Bantuan --}}
    <div class="mt-8">
        <h3 class="px-6 text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Bantuan</h3>
        <div class="bg-white flex flex-col border-t border-gray-50">
            {!! renderMenuItem('ph-chats-circle', 'QnA / Chat Admin', route('qna.index')) !!}
            {!! renderMenuItem('ph-question', 'Pertanyaan Umum (FAQ)', route('faq.index')) !!}
        </div>
    </div>

    {{-- Tombol Logout --}}
    <div class="mt-10 px-6 mb-8">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar?')"
                class="w-full py-3.5 text-red-500 font-bold text-sm bg-red-50 rounded-2xl hover:bg-red-100 hover:text-red-600 transition flex items-center justify-center gap-2 group">
                <i class="ph-bold ph-sign-out text-lg group-hover:scale-110 transition-transform"></i>
                Keluar Aplikasi
            </button>
        </form>
    </div>

</div>
@endsection