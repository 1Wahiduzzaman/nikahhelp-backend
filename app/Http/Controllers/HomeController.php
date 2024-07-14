<?php

namespace App\Http\Controllers;

use App\Models\CandidateInformation;
use App\Models\Country;
use App\Models\Occupation;
use App\Models\Religion;
use App\Models\StudyLevel;
use App\Models\User;
use App\Services\CandidateService;
use App\Services\HomeSearchService;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use CrudTrait;

    protected \App\Services\CandidateService $candidateService;

    private \App\Services\HomeSearchService $searchService;

    /**
     * PurchaseController constructor.
     */
    public function __construct(CandidateService $candidateService, HomeSearchService $searchService)
    {
        $this->candidateService = $candidateService;
        $this->searchService = $searchService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function stripe()
    {
        $user = User::find(2);
        $intent = $user->createSetupIntent();

        return view('stripe', compact('intent'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function recentJoinCandidate(Request $request)
    {
        return $shortListedCandidates = $this->candidateService->reccentJoinCandidate(); //CandidateInformation::where('data_input_status','=',1)->get();

    }

    /**
     * @param  User  $user
     */
    public function filter(Request $request)
    {
        return $this->searchService->filter($request->all());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function initialDropdowns()
    {
        $data['countries'] = Country::where('status', 1)->get();
        $data['religions'] = Religion::where('status', 1)->orderBy('name')->get();
        $data['occupations'] = Occupation::all();
        $data['studylevels'] = StudyLevel::orderBy('name')->get();

        return $this->sendSuccessResponse($data, 'Information fetched successfully');
    }
}
