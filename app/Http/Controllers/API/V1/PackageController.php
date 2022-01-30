<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Generic;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index(){
        $data = Package::where('status', 'Active')->get();     
        
        $active_team_id = Generic::getActiveTeamId();
        $my_team_data = Subscription::with('team')        
        ->where('plan_id', 1)
        ->where('user_id', Auth::id())
        ->first();
        $result = [
            'plan_data' => $data,
            'team_data' => $my_team_data
        ];        
        return $this->sendSuccessResponse($result, 'Data Fetched Successfully');
    }
}
