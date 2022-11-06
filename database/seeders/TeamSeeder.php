<?php

namespace Database\Seeders;

use App\Models\CandidateInformation;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Dompdf\Canvas;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory();

            // $user->has(CandidateInformation::factory()->count(1), 'getCandidate')
            // ->has(TeamMember::factory()->for(Team::factory()->for($user, 'userTeam'), 'userTeam'))
            // ->count(1)->create();
            // User::factory()->has(CandidateInformation::factory()->count(1), 'candidate_info')
            // ->has(Team::factory()->has(TeamMember::factory()->count(2), 'team_members'), 'teams')->create();

            CandidateInformation::factory()->for(User::factory()->has(TeamMember::factory()->for(Team::factory()), 'team_member'))->count(3)->create();

    }
}
