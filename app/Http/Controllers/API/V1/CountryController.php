<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\CandidateService;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;

class CountryController extends Controller
{

    /**
     * @var CandidateService
     */
    protected $countryService;

    /**
     * PurchaseController constructor.
     * @param CountryService $countryService
     */
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->countryService->getCountries();
    }

    /**
     * @return JsonResponse
     */
    public function createCity(CityRequest $request)
    {
        return $this->countryService->saveCity($request->all());
    }


}
