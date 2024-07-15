<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\Visit;
use DateTime;
use Exception;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function visit(Request $request)
    {
        try {
            //code...

            $request->validateWithBag('request failed', [
                'from_team_id' => 'required|numeric',
                'to_team_id' => 'array|required',
                'country' => 'string',
            ]);

            collect($request->input('to_team_id'))->each(function ($data) use ($request) {
                Visit::create([
                    'from_team_id' => $request->input('from_team_id'),
                    'to_team_id' => $data,
                    'country' => $request->input('country'),
                ]);
            });

            return $this->sendSuccessResponse(['saved' => 'successfuly'], HttpStatusCode::SUCCESS->value);

        } catch (Exception $th) {
            return $this->sendErrorResponse($th->getMessage(), HttpStatusCode::INTERNAL_ERROR->value);
        }
    }

    public function visitGraph(Request $request, string $id)
    {
        try {
            $visits = Visit::where('to_team_id', $id)->get();
            $startDate = DateTime::createFromFormat('Y-m-d', date('Y') - 1 .'-'.date('m').'-'.date('d'));
            $endDate = DateTime::createFromFormat('Y-m-d', date('Y').'-'.date('m').'-'.date('d'));

            $viewsInLastYear = [];
            $countryWiseViews = [];
            $months = ['Jan' => 0,
                'Feb' => 0,
                'Mar' => 0,
                'Apr' => 0,
                'May' => 0,
                'Jun' => 0,
                'Jul' => 0,
                'Aug' => 0,
                'Sep' => 0,
                'Oct' => 0,
                'Nov' => 0,
                'Dec' => 0,
            ];
            foreach ($visits as $object) {
                $objectDate = $object->updated_at;
                if ($objectDate >= $startDate && $objectDate <= $endDate) {
                    $viewsInLastYear[] = $object;
                    if (isset($countryWiseViews[$object->country])) {
                        $countryWiseViews[$object->country] += 1;
                    } else {
                        $countryWiseViews[$object->country] = 1;
                    }
                    $months[$objectDate->format('M')] += 1;
                }
            }

            arsort($countryWiseViews);
            $countryWiseViews = array_slice($countryWiseViews, 0, 20);

            $startDate = DateTime::createFromFormat('Y-m-d', date('Y').'-'.date('m') - 1 .'-'.date('d'));
            $endDate = DateTime::createFromFormat('Y-m-d', date('Y').'-'.date('m').'-'.date('d'));
            $viewsInLastMonth = [];
            $monthDays = [
                '1' => 0,
                '2' => 0,
                '3' => 0,
                '4' => 0,
                '5' => 0,
                '6' => 0,
                '7' => 0,
                '8' => 0,
                '9' => 0,
                '10' => 0,
                '11' => 0,
                '12' => 0,
                '13' => 0,
                '14' => 0,
                '15' => 0,
                '16' => 0,
                '17' => 0,
                '18' => 0,
                '19' => 0,
                '20' => 0,
                '21' => 0,
                '22' => 0,
                '23' => 0,
                '24' => 0,
                '25' => 0,
                '26' => 0,
                '27' => 0,
                '28' => 0,
                '29' => 0,
                '30' => 0,
                '31' => 0,
            ];
            foreach ($viewsInLastYear as $object) {
                $objectDate = $object->updated_at;
                if ($objectDate >= $startDate && $objectDate <= $endDate) {
                    $viewsInLastMonth[] = $object;
                    $monthDays[$objectDate->format('j')] += 1;
                }
            }

            $startDate = DateTime::createFromFormat('Y-m-d', date('Y').'-'.date('m').'-'.date('d') - 7);
            $endDate = DateTime::createFromFormat('Y-m-d', date('Y').'-'.date('m').'-'.date('d'));
            $viewsInLastWeek = [];
            $weekDays = [
                'Sun' => 0,
                'Mon' => 0,
                'Tue' => 0,
                'Wed' => 0,
                'Thu' => 0,
                'Fri' => 0,
                'Sat' => 0,
            ];
            foreach ($viewsInLastMonth as $object) {
                $objectDate = $object->updated_at;
                if ($objectDate >= $startDate && $objectDate <= $endDate) {
                    $viewsInLastWeek[] = $object;
                    $weekDays[$objectDate->format('D')] += 1;
                }
            }

            return $this->sendSuccessResponse(['week' => $weekDays, 'months' => $months, 'days' => $monthDays, 'countryWiseViews' => $countryWiseViews], 'Hit counted successfully');
        } catch (\Exception $th) {
            //throw $th;
            return $this->sendErrorResponse($th->getMessage());
        }

    }
}
