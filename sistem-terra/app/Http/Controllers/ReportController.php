<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if($user->role == 'petani') {
            $reports = Report::where('user_id', $user->id)
                             ->with('messages.user')
                             ->latest()
                             ->get();
            return view('reports.petani', compact('reports'));
        } 
        elseif($user->role == 'teknisi') {
            $reports = Report::with(['user', 'messages.user'])
                             ->latest()
                             ->get();
            return view('reports.teknisi', compact('reports'));
        }
        abort(403);
    }

    public function store(Request $request)
    {
        // 1. Buat Laporan (Parent)
        $report = Report::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        // 2. Buat Pesan Pertama (Child)
        ReportMessage::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'message' => $request->description
        ]);

        return back()->with('success', 'Tiket laporan dibuat.');
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required']);

        // 1. Simpan Pesan Baru
        ReportMessage::create([
            'report_id' => $id, // Langsung pakai ID dari URL
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        // 2. Opsional: Ubah status jadi 'pending' lagi kalau petani yang balas
        // Supaya teknisi tau ada balasan baru
        if(Auth::user()->role == 'petani'){
            $report = Report::find($id);
            $report->update(['status' => 'pending']);
        }

        return back()->with('success', 'Pesan terkirim.');
    }

    public function resolve($id)
    {
        // JURUS ANTI ERROR: Pakai cara Query Builder
        // "Cari tabel reports yang id-nya sekian, lalu update statusnya"
        Report::where('id', $id)->update(['status' => 'resolved']);
        
        return back()->with('success', 'Masalah ditandai selesai.');
    }
}