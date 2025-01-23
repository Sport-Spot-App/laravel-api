<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $sports = [
            [
                'name' => 'Futsal',
                'photo' => 'https://sportspott.tech/assets/images/sports/futsal.png',
            ],
            [
                'name' => 'Futebol',
                'photo' => 'https://sportspott.tech/assets/images/sports/futebol.png',
            ],
            [
                'name' => 'Vôlei',
                'photo' => 'https://sportspott.tech/assets/images/sports/volei.png',
            ],
            [
                'name' => 'Beach Tênis',
                'photo' => 'https://sportspott.tech/assets/images/sports/beach-tenis.png',
            ],
            [
                'name' => 'Tênis',
                'photo' => 'https://sportspott.tech/assets/images/sports/tenis.png',
            ],
            [
                'name' => 'Handebol',
                'photo' => 'https://sportspott.tech/assets/images/sports/handebol.png',
            ],
            [
                'name' => 'Basquete',
                'photo' => 'https://sportspott.tech/assets/images/sports/basquete.png',
            ],
       ];
    }
}
