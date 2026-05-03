<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View; // Wajib untuk Return Type
use Illuminate\Http\RedirectResponse; // Wajib untuk Return Type
use Illuminate\Support\Facades\DB; // ✅ FIX: Wajib import untuk Transaksi Database

class EventListController extends Controller
{
    public function __construct()
    {
        // Tetap menggunakan middleware auth dan inline admin check
        // (Meskipun sebaiknya menggunakan middleware('admin') di route group)
        $this->middleware('auth');

        $this->middleware(function (Request $request, $next) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            // Cek apakah user ada DAN apakah dia admin
            if (! $user || ! $user->isAdmin()) {
                abort(403, 'Akses Ditolak. Anda harus menjadi Admin.');
            }

            return $next($request);
        });
    }

    public function index(): View
    {
        // Query akan tetap 4 kali, namun fungsional
        $allEvents = Event::orderBy('created_at', 'desc')->get();
        $pendingEvents = Event::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $approvedEvents = Event::where('status', 'approved')->orderBy('created_at', 'desc')->get();
        $rejectedEvents = Event::where('status', 'rejected')->orderBy('created_at', 'desc')->get();

        return view('admin.event-list', compact(
            'allEvents',
            'pendingEvents',
            'approvedEvents',
            'rejectedEvents'
        ));
    }

    public function approve(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);

        // ✅ FIX: Gunakan Transaksi Database untuk memastikan Event dan Registrasi ter-update semua
        DB::transaction(function () use ($event) {
            // 1. approve event
            $event->status = 'approved';
            $event->save();
            // 2. approve semua registrasi yang terkait dengan event ini
            EventRegistration::where('event_id', $event->id)->update([
                'status' => 'approved',
            ]);
            // 3. Jika event ini memiliki kategori yang masih pending, otomatis setujui juga kategorinya
            if ($event->category_id) {
                $category = \App\Models\Category::find($event->category_id);
                
                if ($category && $category->status === 'pending') {
                    $category->update([
                        'status' => 'approved'
                    ]);
                }
            }
        });
        // -----------------------------------------------------------------------------------

        return redirect()->route('admin.event-list')
            ->with('success', 'Event ' . $event->event_title . ' berhasil disetujui.');
    }

    public function reject(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);

        // ✅ FIX: Gunakan Transaksi Database untuk memastikan Event dan Registrasi ter-update semua
        DB::transaction(function () use ($event) {
            $event->status = 'rejected';
            $event->save();

            EventRegistration::where('event_id', $event->id)->update([
                'status' => 'rejected',
            ]);
        });
        // -----------------------------------------------------------------------------------

        return redirect()->route('admin.event-list')
            ->with('success', 'Event ' . $event->event_title . ' berhasil ditolak.');
    }

    // Detail event untuk admin
    public function show(int $id): View
    {
        // Load relasi registrations dan user
        $event = Event::with('registrations', 'user')->findOrFail($id);

        return view('admin.event-detail', compact('event'));
    }

    public function delete(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        
        // Cek dulu apakah ada relasi yang harus dihapus secara manual (jika tidak ada cascade di DB)
        // Karena event_registrations menggunakan onDelete('cascade'), ini aman.
        $event->delete();

        return redirect()->back()->with('success', 'Event berhasil dihapus.');
    }
}