<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\KonsultasiMessage;
use Illuminate\Http\Request;

class KonsultasiMessageController extends Controller
{
    public function index(Request $request, $id)
    {
        $konsultasi = Konsultasi::findOrFail($id);
        
        // Authorization
        if ($request->user()->isPasien() && $konsultasi->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        if ($request->user()->isDokter() && $konsultasi->dokter_id !== null && $konsultasi->dokter_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $messages = $konsultasi->messages()->with('sender')->get()->map(function($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_role' => $msg->sender->role,
                'message' => $msg->message,
                'created_at' => $msg->created_at,
            ];
        });

        return response()->json($messages);
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $konsultasi = Konsultasi::findOrFail($id);

        // Authorization
        if ($request->user()->isPasien() && $konsultasi->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        if ($request->user()->isDokter() && $konsultasi->dokter_id !== null && $konsultasi->dokter_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($konsultasi->status === 'done') {
            return response()->json(['message' => 'Konsultasi ini sudah selesai dan tidak dapat menerima pesan baru.'], 400);
        }

        // If it's a doctor and konsultasi doesn't have a doctor yet, claim it
        if ($request->user()->isDokter() && $konsultasi->dokter_id === null) {
            $konsultasi->update(['dokter_id' => $request->user()->id]);
        }

        $msg = KonsultasiMessage::create([
            'konsultasi_id' => $konsultasi->id,
            'sender_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        // Also update the 'jawaban_dokter' if doctor replied, just for backward compatibility or we can leave it
        if ($request->user()->isDokter() && !$konsultasi->jawaban_dokter) {
            $konsultasi->update([
                'jawaban_dokter' => $request->message,
                'dijawab_at' => now(),
                'status' => 'in_review' // Changed from 'done' to 'in_review' meaning active chat
            ]);
        }

        // If patient replied and status was received or in_review, make sure status is correct
        if ($request->user()->isPasien() && $konsultasi->status === 'received' && $konsultasi->dokter_id) {
            $konsultasi->update(['status' => 'in_review']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_role' => $request->user()->role,
                'message' => $msg->message,
                'created_at' => $msg->created_at,
            ]
        ]);
    }
}
