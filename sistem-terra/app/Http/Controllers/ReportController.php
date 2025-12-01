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
        $report = Report::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending'
        ]);
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

        ReportMessage::create([
            'report_id' => $id, 
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);
        if(Auth::user()->role == 'petani'){
            $report = Report::find($id);
            $report->update(['status' => 'pending']);
        }

        return back()->with('success', 'Pesan terkirim.');
    }

    public function resolve($id)
    {
        Report::where('id', $id)->update(['status' => 'resolved']);
        return back()->with('success', 'Masalah ditandai selesai.');
    }
}