<?php

namespace Database\Seeders;

use App\Models\TicketSubmission;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TicketSubmission::factory()->count(50)->create();
    }
}
