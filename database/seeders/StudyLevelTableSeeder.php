<?php

namespace Database\Seeders;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class StudyLevelTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('study_level')->delete();

        $religions = [
            ['name' => 'Elementary education'],
            ['name'=>"Primary education"],
            ['name'=>"Secondary education or high school"],
            ['name'=>"General educational diploma"],
            ['name'=>"Vocational qualification"],
            ['name'=>"Professional qualification"],
            ['name'=>"Bachelor's degree or Equivalent"],
            ['name'=>"Master's degree or Equivalent"],
            ['name'=>"PhD (Doctorate)"],
            ['name'=>"Degree + professional qualification"],
            ['name'=>"Other"]
            ];

        DB::table('study_level')->insert($religions);
    }
}
