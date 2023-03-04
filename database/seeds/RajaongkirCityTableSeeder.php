<?php

use Illuminate\Database\Seeder;

class RajaongkirCityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = base_path().'/public/seed/rajaongkir_cities.sql';
        $sql = file_get_contents($path);
        \DB::unprepared($sql);
    }
}