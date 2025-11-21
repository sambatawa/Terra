<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Ambil semua user KECUALI dirinya sendiri (teknisi)
        $users = User::where('role', '!=', 'teknisi')->latest()->get();
        return view('admin.users', compact('users'));
    }

    public function destroy($id)
    {
        User::destroy($id);
        return back()->with('success', 'User berhasil dihapus dari sistem.');
    }
}