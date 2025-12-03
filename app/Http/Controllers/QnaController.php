<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class QnaController extends Controller
{
    // Menampilkan halaman chat & riwayat pesan
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua chat milik user yang sedang login, urutkan dari yang terlama ke terbaru
        $messages = ChatMessage::where('user_id', $userId)
                        ->orderBy('created_at', 'asc')
                        ->get();

        return view('qna.index', compact('messages'));
    }

    // Menyimpan pesan baru (Kirim)
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        ChatMessage::create([
            'user_id' => Auth::id(),
            'sender'  => 'user', // Pengirim adalah user
            'message' => $request->message,
        ]);

        // (Opsional) Auto-reply bot sederhana untuk demo
        // ChatMessage::create([
        //     'user_id' => Auth::id(),
        //     'sender'  => 'admin',
        //     'message' => 'Terima kasih, pesan Anda sudah kami terima. Admin akan segera membalas.',
        // ]);

        return redirect()->route('qna.index');
    }
}
