<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Pedro', 'María', 'Juan', 'Ana', 'Luis', 'Laura', 'Carlos', 'Sofía', 'Diego', 'Elena',
            'Javier', 'Isabel', 'Miguel', 'Lucía', 'Pablo', 'Valeria', 'Daniel', 'Carmen', 'Alejandro', 'Rosa'
        ];

        for ($i = 0; $i < 20; $i++) {
            $name = $names[$i];
            $email = strtolower($name) . ($i + 1) . '@example.com';

            User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'latitude' => $this->generateRandomLatitude(),
                'longitude' => $this->generateRandomLongitude(),
            ]);
        }
    }

    private function generateRandomLatitude()
    {
        return mt_rand(30000000, 40000000) / 1000000; // Latitud entre 30 y 40
    }

    private function generateRandomLongitude()
    {
        return mt_rand(-90000000, -81000000) / 1000000; // Longitud entre -80 y -90
    }
}
