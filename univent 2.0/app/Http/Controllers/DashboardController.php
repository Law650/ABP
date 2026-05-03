<?php

namespace App\Http\Controllers;

use Illuminate\View\View; // Import di atas

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama.
     */
    public function index(): View
    {

        $events = \App\Models\Event::where('status', 'approved')->latest()->get();

        return view('dashboard.dashboard', compact('events')); // ✅ kirim

    }
}
