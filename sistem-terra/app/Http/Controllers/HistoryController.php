<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Detection;
use App\Models\ProductClick;
use App\Models\Post;
use App\Models\User;

class HistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $data = [];

        // 1. LOGIKA PETANI & TEKNISI (Lihat Deteksi & Sensor)
        if ($role == 'petani' || $role == 'teknisi') {
            $data['detections'] = Detection::where('user_id', $user->id)->latest()->get();
            // Dummy Sensor Data
            $data['sensors'] = [
                ['time' => now()->subMinutes(10), 'suhu' => 29.5, 'kelembaban' => 60, 'status' => 'Normal'],
                ['time' => now()->subMinutes(30), 'suhu' => 31.2, 'kelembaban' => 55, 'status' => 'Warning'],
                ['time' => now()->subHour(1), 'suhu' => 28.8, 'kelembaban' => 65, 'status' => 'Normal'],
            ];
        } 
        // 2. LOGIKA PENJUAL (Lihat Klik Produk)
        elseif ($role == 'penjual') {
            $data['clicks'] = ProductClick::where('seller_id', $user->id)->latest()->get();
            $data['total_clicks'] = $data['clicks']->count();
        } 
        // 3. LOGIKA PENYULUH (Lihat Aktivitas Forum) -> INI YANG BARU
        elseif ($role == 'penyuluh') {
            $data['posts'] = Post::where('user_id', $user->id)
                                 ->withCount('comments', 'likes')
                                 ->latest()
                                 ->get();
            
            // Hitung total interaksi (Like + Komen yang diterima)
            $data['total_posts'] = $data['posts']->count();
            $data['total_interactions'] = $data['posts']->sum('likes_count') + $data['posts']->sum('comments_count');
        }

        return view('history.index', compact('data', 'role'));
    }

    // FUNGSI EXPORT CSV (UPDATE JUGA BIAR PENYULUH BISA DOWNLOAD)
    public function export()
    {
        $user = Auth::user();
        $role = $user->role;
        $fileName = 'Laporan-Terra-' . ucfirst($role) . '-' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [];
        $query = [];

        if ($role == 'petani' || $role == 'teknisi') {
            $columns = ['Waktu', 'Hasil Deteksi', 'Confidence', 'Status'];
            $query = Detection::where('user_id', $user->id)->latest()->get();
        } elseif ($role == 'penjual') {
            $columns = ['Waktu', 'Nama Produk', 'Ket'];
            $query = ProductClick::where('seller_id', $user->id)->latest()->get();
        } elseif ($role == 'penyuluh') {
            $columns = ['Tanggal Post', 'Isi Konten', 'Jumlah Like', 'Jumlah Komentar'];
            $query = Post::where('user_id', $user->id)->withCount('comments', 'likes')->latest()->get();
        }

        $callback = function() use ($query, $columns, $role) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($query as $row) {
                $dataRow = [];
                if ($role == 'petani' || $role == 'teknisi') {
                    $dataRow = [$row->created_at, $row->label, $row->confidence . '%', 'Recorded'];
                } elseif ($role == 'penjual') {
                    $dataRow = [$row->created_at, $row->product_name, 'User Click WA'];
                } elseif ($role == 'penyuluh') { // DATA CSV PENYULUH
                    $dataRow = [
                        $row->created_at->format('Y-m-d H:i'),
                        substr($row->content, 0, 50) . '...', // Potong teks biar rapi
                        $row->likes_count,
                        $row->comments_count
                    ];
                }
                fputcsv($file, $dataRow);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // (Simpan fungsi storeDetection & trackClick jangan dihapus/ubah)
    public function storeDetection(Request $request) {
        Detection::create(['user_id' => Auth::id(), 'label' => $request->label, 'confidence' => $request->confidence]);
        return response()->json(['success' => true]);
    }
    public function trackClick(Request $request) {
        ProductClick::create(['seller_id' => $request->seller_id, 'product_name' => $request->product_name]);
        return response()->json(['success' => true]);
    }
}