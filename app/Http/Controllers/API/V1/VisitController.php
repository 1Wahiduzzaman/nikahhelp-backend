<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\Generic;
use App\Models\Visit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr;

class VisitController extends Controller
{
    public function visit(Request $request)
    {
        try {
            //code...

            $request->validateWithBag('request failed', [
                'from_team_id' => 'string|required',
                'to_team_id' => 'array|required'
            ]);

            collect($request->input('to_team_id'))->each(function ($data) use  ($request) {
                    Visit::create([
                        'from_team_id' => $request->input('from_team_id'),
                        'to_team_id' => $data
                    ]);
            });
            return $this->sendSuccessResponse(['saved' => 'successfuly'], HttpStatusCode::SUCCESS);

        } catch (Exception $th) {
            return $this->sendErrorResponse($th->getMessage(), HttpStatusCode::INTERNAL_ERROR);
        }
    }

    public function visitGraph(Request $request, $id)
    {
        try {
            //code...
            $request->validate([
                'id' => 'required|string',
            ]);
            $visits =  Visit::where('to_team_id', $request->input('id'))->get();

            return $this->sendSuccessResponse($visits, 'Hit counted successfully');
        } catch (\Exception $th) {
            //throw $th;
            return $this->sendErrorResponse($th->getMessage());
        }

    }
}
