<?php

namespace Database\Seeders;

// use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call your custom seeder here
        $this->call([
            DokterSeeder::class,
            JadwalDokterSeeder::class,
        ]);
        
        // You can leave the factory test user here if you still want it, 
        // or delete it if you only want the Doctor and Admin.
        /*
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        */
    }
}