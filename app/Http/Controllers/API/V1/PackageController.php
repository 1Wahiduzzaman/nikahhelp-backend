<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\Generic;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index()
    {
        $team_id = (new Generic())->getActiveTeamId();

        if (Subscription::where('team_id', $team_id)->first()->subscription_expire_at) {
            return $this->sendErrorResponse('Can not subscribe same Team until expiry', [], HttpStatusCode::FORBIDDEN->value);
        };

        $data = Package::where('status', 'Active')->get();

        $my_team_data = Subscription::select('team_id')
            ->where('plan_id', 1)
            ->where('user_id', Auth::id())
            ->pluck('team_id')
            ->toArray();

        $data[0]['team_ids'] = $my_team_data;

        return $this->sendSuccessResponse($data, 'Data Fetched Successfully');
    }
}
