<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorasSalaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('horas_sala')->insert(
            [
                'hora' => "8:00"
            ],
            [
                'hora' => "8:30"
            ],
            [
                'hora' => "9:00"
            ],
            [
                'hora' => "9:30"
            ],
            [
                'hora' => "10:00"
            ],
            [
                'hora' => "10:30"
            ],
            [
                'hora' => "11:00"
            ],
            [
                'hora' => "11:30"
            ],
            [
                'hora' => "12:00"
            ],
            [
                'hora' => "12:30"
            ],
            [
                'hora' => "13:00"
            ],
            [
                'hora' => "13:30"
            ],
            [
                'hora' => "14:00"
            ],
            [
                'hora' => "15:30"
            ],
            [
                'hora' => "16:00"
            ],
            [
                'hora' => "16:30"
            ],
            [
                'hora' => "17:00"
            ],
            [
                'hora' => "17:30"
            ],
            [
                'hora' => "18:00"
            ],
            [
                'hora' => "18:30"
            ],
        );
    }
}
