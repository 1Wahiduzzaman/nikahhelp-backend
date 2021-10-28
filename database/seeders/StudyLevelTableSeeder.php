<?php

namespace Database\Seeders;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class StudyLevelTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('study_level')->delete();

        $religions = array(
            array('name' => 'Undergraduate'),
            array('name' => 'Graduate'),
            array('name' => 'Postgraduate')
        );

        DB::table('study_level')->insert($religions);
    }
}
