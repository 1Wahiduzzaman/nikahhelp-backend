<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Package::create(
            [
                'title' => '1 Month',
                'description' => null,
                'price' => 10,
                'discount' => 2,
                'promo_code' => null,
            ]
        );

        Package::create(            
            [
                'title' => '3 Months',
                'description' => null,
                'price' => 30,
                'discount' => 6,
                'promo_code' => null,
            ]
        );
        Package::create(
            [
                'title' => '6 Months',
                'description' => null,
                'price' => 60,
                'discount' => 12,
                'promo_code' => null,
            ]
        );
        Package::create(            
            [
                'title' => '1 Year',
                'description' => null,
                'price' => 120,
                'discount' => 24,
                'promo_code' => null,
            ]
        );
    }
}
