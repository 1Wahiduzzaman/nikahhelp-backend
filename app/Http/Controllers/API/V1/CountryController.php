<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Services\CandidateService;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    /**
     * @var CandidateService
     */
    protected \App\Services\CountryService $countryService;

    /**
     * PurchaseController constructor.
     */
    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index(): JsonResponse
    {
        return $this->countryService->getCountries();
    }

    /**
     * Get cities of country
     *
     * @param  int  $countryIid
     */
    public function getCities(int $countryId): JsonResponse
    {
        return $this->countryService->getCities($countryId);
    }

    /**
     * @return JsonResponse
     */
    public function createCity(CityRequest $request)
    {
        return $this->countryService->saveCity($request->all());
    }

    public function getCityList()
    {
        try {
            $cities = City::all();

            return $this->sendSuccessResponse($cities, 'city name save successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }
}
