<?php

namespace Database\Seeders;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ReligionsTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('religions')->delete();

        $religions = [
            ['name' => "Other"],
            ['name' => "Baha'i"],
            ['name' => "Buddhism"],
            ['name' => "Candomblé"],
            ['name' => "Catholicism"],
            ['name' => "Christianity"],
            ['name' => "Church of the East"],
            ['name' => "Conservative Judaism"],
            ['name' => "Eastern Orthodoxy"],
            ['name' => "Eastern Protestant Christianity"],
            ['name' => "Hasidic Judaism"],
            ['name' => "Heredi Orthodox"],
            ['name' => "Hinduism"],
            ['name' => "Humanistic Judaism"],
            ['name' => "Islam"],
            ['name' => "Islam Shia"],
            ['name' => "Islam Sunni"],
            ['name' => "Jainism"],
            ['name' => "Jewish Renewal"],
            ['name' => "Judaism"],
            ['name' => "Mormonism"],
            ['name' => "Non-trinitarian Restorationism"],
            ['name' => "Open Orthodox"],
            ['name' => "Oriental Orthodoxy"],
            ['name' => "Orthodox Judaism"],
            ['name' => "Protestantism"],
            ['name' => "Rastafari"],
            ['name' => "Reconstructionist Judaism"],
            ['name' => "Reform Judaism"],
            ['name' => "Santeria"],
            ['name' => "Shinto"],
            ['name' => "Sikhism"],
            ['name' => "Taoism"],
            ['name' => "Unitarianism"],
            ['name' => "Yeshivish Judaism"],
            ['name' => "Zoroastrianism"],
        ];

        DB::table('religions')->insert($religions);
    }
}
