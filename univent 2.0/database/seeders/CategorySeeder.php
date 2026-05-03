<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar kategori default 
        $defaultCategories = [
            'Seminar',
            'Workshop',
            'Competition',
            'Gathering'
            
        ];

        foreach ($defaultCategories as $categoryName) {
            // Gunakan firstOrCreate agar tidak ada duplikat jika seeder dijalankan 2x
            Category::firstOrCreate(
                ['name' => $categoryName],
                ['status' => 'approved'] // Langsung otomatis disetujui
            );
        }
    }
}