<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call(ProvinciasSeeder::class);
       $this->call(UserSeeder::class);
       $this->call(GestorDocumentalTableSeeder::class);
       $this->call(DragTableSeeder::class);
       //$this->call(IngresosSeed::class);
    }
}
