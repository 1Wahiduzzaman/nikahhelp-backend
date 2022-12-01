<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Generic;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    public function visit(Request $request){
        $active_team_id = (new Generic())->getActiveTeamId();
        if($active_team_id!=$request->from_team_id || $active_team_id!=$request->to_team_id) {
            $conditions = [
                'from_team_id' => $request->from_team_id,
                'to_team_id' => $request->to_team_id,
                'user_id' => Auth::id(),
            ];
            $visit_count = Visit::where('from_team_id', $request->from_team_id)
            ->where('to_team_id', $request->to_team_id)
            ->where('user_id', Auth::id())
            ->first();
            if(isset($visit_count->visit_count) && !empty($visit_count->visit_count)) {
                $visit = $visit_count->visit_count + 1;
            } else {
                $visit = 1;
            }
            $data = [
                'from_team_id' => $request->from_team_id,
                'to_team_id' => $request->to_team_id,
                'user_id' => Auth::id(),
                'visit_count' => $visit,
            ];
            Visit::updateOrCreate($conditions, $data);
            return $this->sendSuccessResponse([], 'Hit counted successfully');
        }
    }

    public function visitGraph(Request $request) {
        // $data = Visit::
        // select(["SUM(visit_count)", 'created_at', 'to_team_id'])
        // ->where('to_team_id', $active_team_id)
        // ->groupBy('created_at')
        // ->get();
         $visits =  Visit::where('from_team_id', $request->input('from_team_id'))->get();
        // $data = Visit::select(
        //     DB::raw("(sum(visit_count)) as view"),
        //     // DB::raw("(DATE_FORMAT(created_at, '%d-%m-%Y')) as my_date")
        //     DB::raw("(DATE_FORMAT(created_at, '%M')) as my_date")
        //     )
        //     ->orderBy(DB::raw("DATE_FORMAT(created_at, '%m')"))
        //     // ->groupBy(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))
        //     ->groupBy(DB::raw("DATE_FORMAT(created_at, '%m')"))
        //     ->get();
        //     $result = [
        //         'view' => $data->pluck('view')->toArray(),
        //         'date' => $data->pluck('my_date')->toArray(),
        //     ];
            return $this->sendSuccessResponse($visits, 'Hit counted successfully');
    }
}
