<?php

namespace Database\Seeders;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ReligionsTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('religions')->delete();

        $religions = array(
            array('name' => 'Atheists'),
            array('name' => 'Agnostics'),
            array('name' => 'Bahais'),
            array('name' => 'Buddhists'),
            array('name' => 'Chinese folk-religionists'),
            array('name' => 'Christians'),
            array('name' => 'Confucianists'),
            array('name' => 'Daoists'),
            array('name' => 'Ethnoreligionists'),
            array('name' => 'Hindus'),
            array('name' => 'Jains'),
            array('name' => 'Jews'),
            array('name' => 'Muslims'),
            array('name' => 'New Religionists'),
            array('name' => 'Shintoists'),
            array('name' => 'Sikhs'),
            array('name' => 'Spiritists'),
            array('name' => 'Zoroastrians'),
        );

        DB::table('religions')->insert($religions);
    }
}
