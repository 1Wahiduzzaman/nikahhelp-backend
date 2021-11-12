<?php


namespace App\Services;

use App\Models\City;
use App\Repositories\CountryRepository;
use Exception;
use Illuminate\Http\JsonResponse;



class CountryService extends ApiBaseService
{


    /**
     * @var CountryRepository
     */
    protected $countryRepository;


    /**
     * UserService constructor.
     *
     * @param CountryRepository $countryRepository
     */
    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * Get all country list
     * @return JsonResponse
     */
    public function getCountries(): JsonResponse
    {
        try {
            $data = $this->countryRepository->findAll()->where('status','=',1);
//            $data = CountryCityResource::collection($data); //commented by rabbi
            return $this->sendSuccessResponse($data, 'Information fetched Successfully!');
        }catch (Exception $exception){
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Get all cities of the country (id)
     * @param $countryId
     * @return JsonResponse
     */
    public function getCities($countryId): JsonResponse
    {
        try {
            $cities = City::where('country_id',$countryId)->get();
            return $this->sendSuccessResponse($cities, 'Information fetched Successfully!');
        }catch (Exception $exception){
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function saveCity($request){
        try {
            if(isset($request['id']) && !empty($request['id'])):
                $saveData= new City($request['id']);
             else:
            $saveData= new City();
             endif;
            $saveData->country_id = $request['country_id'];
            $saveData->name = $request['name'];
            $saveData->save();
            return $this->sendSuccessResponse($saveData, 'city name save successfully!');
        }catch (Exception $exception){
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

}
