<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index()
    {
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
