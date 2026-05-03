<?php

namespace App\Http\Controllers;

use App\Mail\ContactNotification;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View; // Import untuk return type
use Illuminate\Http\RedirectResponse; // Import untuk return type

class ContactController extends Controller
{
    public function __construct()
    {
        // Tetap menggunakan middleware auth
        $this->middleware('auth');

        // Admin tidak boleh membuka /contact atau mengirim pesan
        $this->middleware(function (Request $request, $next) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            // Gunakan helper method isAdmin()
            if ($user && $user->isAdmin()) {
                // Admin diarahkan ke dashboard (asumsi dashboard user biasa)
                return redirect()->route('dashboard')
                    ->with('error', 'Admin tidak dapat menggunakan fitur Contact.');
            }

            return $next($request);
        });
    }

    public function create(): View
    {
        return view('contact');
    }

    /**
     * Menyimpan pesan kontak.
     */
    public function store(Request $request): RedirectResponse
    {
        // User pasti ada karena ada middleware 'auth'
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'message' => 'required|string|max:500',
        ]);

        $contact = Contact::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            // Hapus field 'subject' karena tidak ada di DB/Model Anda
            'message' => $validated['message'],
        ]);

        try {
            Mail::to('univenttelkom@gmail.com')->send(new ContactNotification($contact));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email contact: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pesan kontak Anda telah berhasil dikirim!');
    }
}