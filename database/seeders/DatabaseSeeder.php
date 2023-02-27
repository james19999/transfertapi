<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Companies::factory(200)->create();

        \App\Models\Companies::factory()->create([
            'name' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('password'),
            'phone'=>Str::random(10),
            'adress'=>"james",
            'raison'=>'new',
            'domaine'=>"domaine",
            'quartier'=>"quartier",
        ]);
        // DB::table('companies')->insert([
        //     'name' => Str::random(10),
        //     'email' => Str::random(10).'@gmail.com',
        //     'password' => Hash::make('password'),
        //     'phone'=>Str::random(10),
        //     'adress'=>"james",
        //     'raison'=>'new',
        //     'domaine'=>"domaine",
        //     'quartier'=>"quartier",
        // ]);
    }
}