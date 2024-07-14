<?php

namespace Database\Seeders;

use App\Models\RepresentativeInformation;
use App\Models\TicketSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;

class RepresentativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->has(RepresentativeInformation::factory()
                ->has(TicketSubmission::factory()->count(3), 'ticketSubmission')
                ->count(3), 'getRepresentative')->create();

    }
}
