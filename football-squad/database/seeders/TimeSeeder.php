<?php

namespace Database\Seeders;

use App\Models\Time;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $times = [
            ['nome' => 'Vila Nova FC', 'bairro' => 'Vila Nova', 'tecnico' => 'Lucas Moraes'],
            ['nome' => 'Jardim Unidos', 'bairro' => 'Jardim América', 'tecnico' => 'Rones Lucas'],
            ['nome' => 'Real E.C', 'bairro' => 'Centro', 'tecnico' => 'José Gustavo'],
            ['nome' => 'Sampaio Time', 'bairro' => 'Cohab', 'tecnico' => 'Adriano'],
            ['nome' => 'Real Matriz', 'bairro' => 'Vila Operária', 'tecnico' => 'Douglas'],
            ['nome' => 'Aliança Futebol', 'bairro' => 'Bairro Alto', 'tecnico' => 'Marcos'],
            ['nome' => 'Curingas da Norte', 'bairro' => 'Vila Norte', 'tecnico' => 'Sávio Silva'],
            ['nome' => 'União Capão', 'bairro' => 'Vila Capurusso', 'tecnico' => 'Paulo Souza'],
        ];
        
        foreach ($times as $time) {
            Time::create($time);
        }
    }

}
