<?php

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
        // $this->call(UsersTableSeeder::class);
        $this->call(RajaongkirProvinceTableSeeder::class); //Initial Data Rajaongkir Province
        $this->call(RajaongkirCityTableSeeder::class); //Initial Data Rajaongkir City
        $this->call(RajaongkirSubdistrictTableSeeder::class); //Initial Data Rajaongkir Subdistrict
    }
}
