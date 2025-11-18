<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Board;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Create default boards
        Board::create([
            'slug' => 'b',
            'name' => 'Random',
            'description' => 'Random discussion and miscellaneous topics',
        ]);

        Board::create([
            'slug' => 'tech',
            'name' => 'Technology',
            'description' => 'Technology, programming, and computers',
        ]);

        Board::create([
            'slug' => 'g',
            'name' => 'Gaming',
            'description' => 'Video games and gaming discussion',
        ]);

        Board::create([
            'slug' => 'a',
            'name' => 'Anime & Manga',
            'description' => 'Japanese animation and comics',
        ]);

        Board::create([
            'slug' => 'fit',
            'name' => 'Fitness',
            'description' => 'Health, fitness, and sports',
        ]);

        $this->command->info('✓ Created admin user (username: admin, password: password)');
        $this->command->info('✓ Created 5 default boards');
    }
}
