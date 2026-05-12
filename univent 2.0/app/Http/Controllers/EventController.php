<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon; // <-- Tambahan untuk manipulasi waktu/tanggal
use Illuminate\Support\Facades\Http;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only([
            'create',
            'store',
            'update',
            'showHistory',
            'showRegistration',
        ]);
    }

    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    public function create(Request $request): View|RedirectResponse
    {
        $event = null;

        if ($request->has('edit')) {
            $eventId = $request->query('edit');
            /** @var Event $event */
            $event = Event::findOrFail($eventId);

            $isOwner = EventRegistration::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->exists();

            if (! $isOwner) {
                return redirect()->route('user.event.history')->with('error', 'Anda tidak berhak mengubah event ini.');
            }

            if ($event->status !== 'pending') {
                return redirect()->route('user.event.history')->with('error', 'Event yang sudah disetujui tidak dapat diubah.');
            }
        }
        // 2. Ambil daftar kategori yang disetujui untuk dropdown
        $categories = \App\Models\Category::where('status', 'approved')->get();
        // 3. Ambil data EO untuk pre-fill jika user sudah terdaftar sebagai EO
        $eo = \App\Models\EventOrganizer::where('user_id', Auth::id())->first();
        return view('submit-event', compact('event', 'categories', 'eo'));
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi (Hapus eo_name, sesuaikan organizer_type)
        $request->validate([
            'event_title' => 'required|string|max:255',
            'organizer_type' => 'required|string',
            'category_id' => 'required',
            'event_description' => 'required|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'event_location' => 'required|string',
            'registration_link' => 'nullable|url',
            'contact_person' => 'required|string',
            'event_poster' => 'required|image|mimes:jpg,jpeg,png|max:4096', // Tambahkan required
        ]);

        $user = Auth::user();

        // 2. Ambil atau Buat Profil EO dengan Nama User
        $eo = \App\Models\EventOrganizer::where('user_id', $user->id)->first();
        if (!$eo) {
            $eo = \App\Models\EventOrganizer::create([
                'user_id' => $user->id,
                'name' => $user->name, // Mengambil nama dari profil user
                'description' => $request->organizer_type,
            ]);
        }

        // logika kategori baru
        $finalCategoryId = null;
        $categoryName = '';

        if ($request->category_id === 'other') {
            // Jika kategori baru, buat record di tabel categories agar kita dapat ID-nya
            $newCat = \App\Models\Category::firstOrCreate(
                ['name' => $request->new_category_name],
                ['status' => 'pending'] // Set pending karena perlu persetujuan admin
            );
            $finalCategoryId = $newCat->id;
            $categoryName = $newCat->name;
        } else {
            // Jika pilih dari dropdown, ambil ID dan namanya
            $finalCategoryId = $request->category_id;
            $cat = \App\Models\Category::find($request->category_id);
            $categoryName = $cat ? $cat->name : 'Unknown';
        }

        $posterData = null;
        if ($request->hasFile('event_poster')) {
            $file = $request->file('event_poster');
            if ($file && $file->getRealPath()) {
                $content = file_get_contents($file->getRealPath());
                if ($content !== false) {
                    $posterData = base64_encode($content);
                }
            }
        }

        // SIMPAN KE DATABASE
        $event = Event::create([
            'user_id' => $user->id,
            'event_organizer_id' => $eo->id,
            'category_id' => $finalCategoryId,
            'event_title' => $request->event_title,
            'organizer_name' => $user->name, // Gunakan nama user
            'organizer_type' => $request->organizer_type, // Input dari dropdown
            'event_category' => $categoryName,
            'event_description' => $request->event_description,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'event_location' => $request->event_location,
            'registration_link' => $request->registration_link,
            'contact_person' => $request->contact_person,
            'event_poster' => $posterData,
            'status' => 'pending',
    ]);

        if (Auth::check()) {
            EventRegistration::create([
                'user_id' => Auth::id(),
                'event_id' => $event->id,
                'status' => 'pending',
            ]);
        }
        
        // --- TAMBAHAN NOTIFIKASI KE ADMIN ---
        // Cari akun admin (berdasarkan ID 1 dari database-mu)
        $admins = \App\Models\User::where('id', 1)->get(); 
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewEventSubmittedNotification($event));
        // ------------------------------------

        return redirect()->route('dashboard')->with('success', 'Event berhasil disubmit dan terdaftar!');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        // 1. Validasi disesuaikan dengan form baru (tanpa eo_name & eo_description)
        $request->validate([
            'event_title' => 'required|string|max:255',
            'organizer_type' => 'required|string', // <-- Menggunakan input dropdown
            'category_id' => 'required',
            'event_description' => 'required|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'event_location' => 'required|string',
            'registration_link' => 'nullable|url',
            'contact_person' => 'required|string',
            'event_poster' => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // Tetap nullable saat update
        ]);

        $event = Event::findOrFail($id);
        $user = Auth::user();

        // 2. Cek status dan hak akses
        if ($event->status !== 'pending') {
            return redirect()->route('user.event.history')->with('error', 'Event yang sudah disetujui tidak dapat diubah.');
        }

        $isOwner = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $isOwner) {
            return redirect()->route('user.event.history')->with('error', 'Anda tidak berhak mengubah event ini.');
        }

        // 3. Logika Organizer (Ambil dari data user yang sedang login)
        $eo = \App\Models\EventOrganizer::where('user_id', $user->id)->first();
        if (!$eo) {
            $eo = \App\Models\EventOrganizer::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'description' => $request->organizer_type,
            ]);
        } else {
            // Update tipe organisasi jika diubah
            $eo->update(['description' => $request->organizer_type]);
        }

        // 4. Logika Kategori
        $finalCategoryId = null;
        $categoryName = '';

        if ($request->category_id === 'other') {
            $newCat = \App\Models\Category::firstOrCreate(
                ['name' => $request->new_category_name],
                ['status' => 'pending']
            );
            $finalCategoryId = $newCat->id;
            $categoryName = $newCat->name;
        } else {
            $finalCategoryId = $request->category_id;
            $cat = \App\Models\Category::find($request->category_id);
            $categoryName = $cat ? $cat->name : 'Unknown';
        }

        // 5. Logika Poster (Pakai yang lama jika tidak ada upload baru)
        $posterData = $event->event_poster;
        if ($request->hasFile('event_poster')) {
            $file = $request->file('event_poster');
            if ($file && $file->getRealPath()) {
                $content = file_get_contents($file->getRealPath());
                if ($content !== false) {
                    $posterData = base64_encode($content);
                }
            }
        }

        // 6. Simpan perubahan ke Database
        $event->update([
            'event_organizer_id' => $eo->id,
            'category_id' => $finalCategoryId,
            'event_title' => $request->event_title,
            'organizer_name' => $user->name, // Selalu gunakan nama dari profil user
            'organizer_type' => $request->organizer_type,
            'event_category' => $categoryName,
            'event_description' => $request->event_description,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'event_location' => $request->event_location,
            'registration_link' => $request->registration_link,
            'contact_person' => $request->contact_person,
            'event_poster' => $posterData,
        ]);

        return redirect()->route('user.event.history')->with('success', 'Event berhasil diperbarui!');
    }

    public function index(): View
    {
        // 1. Ambil semua event aktif beserta jumlah kliknya
        $activeEvents = Event::where('status', 'approved')
            ->where('end_date', '>=', today())
            ->withCount('clicks') 
            ->get();

        // 2. Terapkan Kalkulator Poin
        $trendingEvents = $activeEvents->map(function ($event) {
            // A. Poin Dasar: 1 Klik = 5 Poin
            $points = $event->clicks_count * 5;

            // B. Poin Kebaruan: Tambah 15 poin jika event dibuat <= 3 hari yang lalu
            if ($event->created_at && $event->created_at->diffInDays(now()) <= 3) {
                $points += 15;
            }

            // C. Poin Urgency: Tambah 20 poin jika event akan kadaluarsa <= 3 hari lagi
            $endDate = Carbon::parse($event->end_date);
            if (now()->diffInDays($endDate) <= 3) {
                $points += 20;
            }

            // Simpan poin sementara ke objek event
            $event->trending_points = $points;
            
            return $event;
        })
        ->sortByDesc('trending_points') // Urutkan dari poin tertinggi
        ->take(3); // Ambil Top 3

        $events = Event::where('status', 'approved')->latest()->get();

        return view('dashboard.dashboard', compact('trendingEvents', 'events'));
    }

    public function show(Request $request, int $id): View 
    {
        $event = Event::findOrFail($id);

        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        DB::table('user_event_clicks')->updateOrInsert(
            [
                'event_id' => $event->id,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ],
            [
                'created_at' => now(), 
                'updated_at' => now()
            ]
        );

        return view('event-detail', compact('event'));
    }

    public function browse(Request $request): View 
    {
        // 1. Tangkap semua input dari form (Search, Kategori, Organizer)
        $search = $request->query('search'); 
        $category = $request->query('category');
        $organizer = $request->query('organizer');

        $recommendedEvents = collect(); 
        $isPersonalized = false; 

        // Daftar kategori utama (untuk logika "Other")
        $mainCategories = ['Seminar', 'Workshop', 'Competition', 'Gathering'];

        /**
         * FUNGSI PENYARING (CLOSURE)
         * Kita buat fungsi ini agar filter Search, Category, dan Organizer 
         * bisa langsung diterapkan ke Main Query, Recommended, maupun Cold Start 
         * tanpa harus menulis ulang kode if-else berkali-kali.
         */
        /**
         * FUNGSI PENYARING (CLOSURE)
         */
        $applyFilters = function ($query) use ($search, $category, $organizer, $mainCategories) {
            // A. Filter Search Bar
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('event_title', 'LIKE', "%{$search}%")
                      ->orWhere('event_location', 'LIKE', "%{$search}%")
                      ->orWhere('event_description', 'LIKE', "%{$search}%");
                });
            }

            // B. Filter Kategori (Perbaikan berdasarkan category_id)
            if ($category) {
                if ($category === 'Other') {
                    // 1. Cari tahu ID dari kategori utama (Seminar, Workshop, dll)
                    $mainCatIds = \App\Models\Category::whereIn('name', $mainCategories)->pluck('id');
                    // 2. Singkirkan event yang memiliki ID tersebut
                    $query->whereNotIn('category_id', $mainCatIds);
                } else {
                    // Cari ID dari kategori spesifik yang dicari user
                    $catId = \App\Models\Category::where('name', $category)->value('id');
                    $query->where('category_id', $catId);
                }
            }

            // C. Filter Organizer
            if ($organizer) {
                $query->where('organizer_type', $organizer);
            }
        };


        // ==========================================
        // 1. QUERY SEMUA EVENT UTAMA
        // ==========================================
        $eventsQuery = Event::where('status', 'approved')
            ->where('end_date', '>=', today());
            
        $applyFilters($eventsQuery); // Terapkan saringan
        
        $events = $eventsQuery->latest()->get();


        // ==========================================
        // 2. QUERY REKOMENDASI FYP (Personalisasi)
        // ==========================================
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        $topCategories = DB::table('user_event_clicks')
            ->join('events', 'user_event_clicks.event_id', '=', 'events.id')
            ->where('user_event_clicks.ip_address', $ipAddress)
            ->where('user_event_clicks.user_agent', $userAgent)
            ->where('user_event_clicks.created_at', '>=', now()->subDays(30))
            ->select('events.category_id', DB::raw('count(*) as total_clicks'))
            ->groupBy('events.category_id')
            ->orderByDesc('total_clicks')
            ->limit(2)
            ->pluck('events.category_id');

        if ($topCategories->isNotEmpty()) {
            $recommendedQuery = Event::where('status', 'approved')
                ->whereIn('category_id', $topCategories)
                ->where('end_date', '>=', today())
                ->withCount('clicks'); 

            $applyFilters($recommendedQuery); // Terapkan saringan

            // Terapkan Kalkulator Poin pada hasil Personalisasi
            $recommendedEvents = $recommendedQuery->get()->map(function ($event) {
                $points = $event->clicks_count * 5; 
                
                if ($event->created_at && $event->created_at->diffInDays(now()) <= 3) {
                    $points += 15; 
                }
                
                $endDate = Carbon::parse($event->end_date);
                if (now()->diffInDays($endDate) <= 3) {
                    $points += 20; 
                }
                
                $event->trending_points = $points;
                return $event;
            })->sortByDesc('trending_points')->take(6);
            
            if ($recommendedEvents->isNotEmpty()) {
                $isPersonalized = true;
            }
        }


        // ==========================================
        // 3. COLD START (Algoritma Trending)
        // ==========================================
        if ($recommendedEvents->isEmpty()) {
            $coldStartQuery = Event::where('status', 'approved')
                ->where('end_date', '>=', today())
                ->withCount('clicks'); 
            
            $applyFilters($coldStartQuery); // Terapkan saringan

            // Terapkan Kalkulator Poin pada Cold Start
            $recommendedEvents = $coldStartQuery->get()->map(function ($event) {
                $points = $event->clicks_count * 5; 
                
                if ($event->created_at && $event->created_at->diffInDays(now()) <= 3) {
                    $points += 15; 
                }
                
                $endDate = Carbon::parse($event->end_date);
                if (now()->diffInDays($endDate) <= 3) {
                    $points += 20; 
                }
                
                $event->trending_points = $points;
                return $event;
            })->sortByDesc('trending_points')->take(6);
        }

        return view('browse-events', compact('recommendedEvents', 'events', 'isPersonalized', 'search'));
    }

    public function showHistory(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
        $userId = Auth::id();
        $registrations = EventRegistration::where('user_id', $userId)
            ->with('event')
            ->latest()
            ->paginate(10);

        return view('event-history', compact('registrations'));
    }

    public function showRegistration(int $id): View
    {
        $registration = EventRegistration::with('event')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('registration-detail', compact('registration'));
    }

    // Tambahkan fungsi ini di dalam class EventController
    public function generateDescription(Request $request)
    {
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'API Key tidak ditemukan.'], 500);
        }

        // Gunakan versi Lite yang biasanya memiliki jatah kuota lebih besar di Free Tier
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        $prompt = "Buatkan deskripsi acara yang sangat menarik untuk mahasiswa Telkom University Purwokerto. 
        Judul Event: '{$request->title}'
        Kategori: '{$request->category_name}'.";

        try {
            $response = Http::withoutVerifying()->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                // Struktur JSON Gemini biasanya tetap sama
                $description = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal generate teks.';
                
                return response()->json([
                    'success' => true, 
                    'description' => trim($description)
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Gagal: ' . $response->body()
            ], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}