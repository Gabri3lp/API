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
        //$this->call(UsersTableSeeder::class);
        //$this->call(UserFactory::class);
        factory(App\User::class, 10)->create();
        DB::table('users')->insert([
            'id' => '26770427',
            'firstName' => 'Gabriel',
            'lastName' => 'Perez',
            'email' => 'gabriel.jp215@gmail.com',
            'password' => bcrypt('12345678'),
            'city' => 'guayana',
            'status' => 'Activo',
            'address' => 'Caimito',
            'phone' => '13232123',
            'cellphone' => '545645',
            'birthDate' => '17-02-1997',
            'role_id' => '1'
        ]);
        factory(App\Hour::class, 50)->create();

    }
}
