@extends('layouts.app')

@section('title', 'Chat Admin')

@section('content')
<div class="bg-gray-50 h-screen flex flex-col relative">

    {{-- 
        1. HEADER (FIXED TOP) 
        - Z-Index tinggi agar di atas chat
    --}}
    <header class="bg-white px-6 pt-8 pb-4 flex items-center gap-4 shadow-sm z-30 sticky top-0">
        <a href="{{ route('profile.index') }}"
            class="text-gray-900 hover:text-[#37967D] transition p-1 -ml-1">
            <i class="ph-bold ph-caret-left text-2xl"></i>
        </a>
        
        <div class="flex items-center gap-3">
            {{-- Avatar Admin --}}
            <div class="w-10 h-10 rounded-full bg-[#37967D]/10 flex items-center justify-center text-[#37967D] border border-[#37967D]/20">
                <i class="ph-fill ph-headset text-xl"></i>
            </div>
            
            <div>
                <h1 class="text-base font-bold text-gray-900 leading-tight">Admin Support</h1>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-xs text-gray-500 font-medium">Online</span>
                </div>
            </div>
        </div>
    </header>

    {{-- 
        2. CHAT AREA (SCROLLABLE)
        - flex-1 agar mengisi sisa ruang
        - pb-24 memberi ruang untuk input bar di bawah
    --}}
    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4 pb-24 scroll-smooth bg-gray-50" id="chatContainer">

        {{-- Tanggal --}}
        <div class="text-center my-4">
            <span class="text-[10px] bg-gray-200 text-gray-500 px-3 py-1 rounded-full font-medium">Hari Ini</span>
        </div>

        @forelse($messages as $msg)
            @if($msg->sender == 'user')
                {{-- Bubble Chat User (Kanan) --}}
                <div class="flex justify-end">
                    <div class="flex flex-col items-end max-w-[75%]">
                        <div class="bg-[#37967D] text-white px-4 py-2.5 rounded-2xl rounded-tr-sm shadow-sm text-sm leading-relaxed">
                            {{ $msg->message }}
                        </div>
                        <div class="flex items-center gap-1 mt-1 mr-1">
                            <span class="text-[10px] text-gray-400">{{ $msg->created_at->format('H:i') }}</span>
                            <i class="ph-bold ph-checks text-[#37967D] text-xs"></i>
                        </div>
                    </div>
                </div>
            @else
                {{-- Bubble Chat Admin (Kiri) --}}
                <div class="flex justify-start">
                    <div class="flex flex-col items-start max-w-[75%]">
                        <div class="bg-white border border-gray-100 text-gray-700 px-4 py-2.5 rounded-2xl rounded-tl-sm shadow-sm text-sm leading-relaxed">
                            {{ $msg->message }}
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1 ml-1">
                            {{ $msg->created_at->format('H:i') }}
                        </span>
                    </div>
                </div>
            @endif
        @empty
            {{-- Tampilan Kosong --}}
            <div class="flex flex-col items-center justify-center h-full text-gray-400 space-y-2 opacity-60">
                <i class="ph-duotone ph-chat-teardrop-text text-4xl"></i>
                <p class="text-xs">Belum ada percakapan.</p>
                <p class="text-[10px]">Mulai chat untuk bertanya.</p>
            </div>
        @endforelse

    </div>

    {{-- 
        3. INPUT AREA (FIXED BOTTOM) 
    --}}
    <div class="fixed bottom-0 w-full bg-white border-t border-gray-100 px-4 py-3 z-30 max-w-[480px] mx-auto left-0 right-0">
        <form action="{{ route('qna.store') }}" method="POST" class="flex items-end gap-2">
            @csrf
            
            {{-- Tombol Attachment --}}
            <button type="button" class="text-gray-400 hover:text-[#37967D] transition p-2 mb-1">
                <i class="ph-bold ph-paperclip text-xl"></i>
            </button>

            {{-- Input Field --}}
            <div class="flex-1 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-2.5 focus-within:border-[#37967D] focus-within:ring-1 focus-within:ring-[#37967D] transition-all flex items-center gap-2">
                <input type="text" name="message" placeholder="Tulis pesan..." autocomplete="off"
                    class="w-full bg-transparent border-none text-gray-900 text-sm focus:ring-0 p-0 placeholder-gray-400"
                    required>
                <i class="ph-fill ph-smiley text-gray-400 text-lg cursor-pointer hover:text-gray-600"></i>
            </div>

            {{-- Send Button --}}
            <button type="submit"
                class="bg-[#37967D] hover:bg-[#2f826c] text-white rounded-full w-11 h-11 flex-shrink-0 flex items-center justify-center shadow-md shadow-[#37967D]/20 transition transform active:scale-95 mb-0.5">
                <i class="ph-fill ph-paper-plane-right text-lg translate-x-[-1px] translate-y-[1px]"></i>
            </button>
        </form>
    </div>

</div>

<script>
    // Auto scroll ke bawah saat halaman dimuat
    document.addEventListener("DOMContentLoaded", function() {
        const chatContainer = document.getElementById('chatContainer');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>
@endsection