<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CandidateInformation;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Traits\CrudTrait;
use App\Services\HomeSearchService;


class HomeController extends Controller
{

    use CrudTrait;

    /**
     * @var CandidateService
     */
    protected $candidateService;

    /**
     * @var  HomeSearchService
     */
    private $searchService;

    /**
     * PurchaseController constructor.
     * @param CandidateService $candidateService
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recentJoinCandidate(Request $request)
    {
        return $shortListedCandidates = $this->candidateService->reccentJoinCandidate();//CandidateInformation::where('data_input_status','=',1)->get();

    }

    /**
     * @param Request $request
     * @param User $user
     */
    public function filter(Request $request)
    {
        return $this->searchService->filter($request->all());
    }
}
