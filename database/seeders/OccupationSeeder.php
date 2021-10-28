<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('occupations')->delete();
        $occupations = array(
            array('name' => 'Architect'),
            array('name' => 'Actor'),
            array('name' => 'Chef/Cook'),
            array('name' => 'Designer'),
            array('name' => 'Doctor'),
            array('name' => 'Electrician'),
            array('name' => 'Engineer'),
            array('name' => 'Factory worker'),
            array('name' => 'Farmer'),
            array('name' => 'Fisherman'),
            array('name' => 'Journalist'),
            array('name' => 'Judge'),
            array('name' => 'Lecturer')
        );
        DB::table('occupations')->insert($occupations);
    }
}
