<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,petani,penjual,penyuluh'],
        ];
        
        // Jika role petani, wajib ada kode unik
        if ($request->role === 'petani') {
            $rules['kode_unik'] = [
                'required', 
                'string', 
                'size:8', // TERRA001 = 8 karakter
                function ($attribute, $value, $fail) {
                    $kodeUnikPetanis = \Cache::get('kode_unik_petanis', []);
                    if (!array_key_exists(strtoupper($value), $kodeUnikPetanis)) {
                        $fail('Kode unik tidak valid. Hubungi admin untuk mendapatkan kode yang benar.');
                    }
                }
            ];
        }
        
        $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
