<?php


namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Models\User;
use App\Models\VerifyUser;
use App\Mail\VerifyMail as VerifyEmail;
use App\Models\City;
use App\Repositories\CountryRepository;
use Carbon\Carbon;
use Exception;
use Mail;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\UserRepository;
use App\Repositories\EmailVerificationRepository as EmailVerifyRepository;
use DB;
use Symfony\Component\HttpFoundation\Response as FResponse;
use App\Http\Resources\CountryCityResource;


class CountryService extends ApiBaseService
{


    /**
     * @var UserRepository
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
     * @return JsonResponse
     */
    public function getCountries(): JsonResponse
    {
        try {
            $data = $this->countryRepository->findAll()->where('status','=',1);
            $data = CountryCityResource::collection($data);
            return $this->sendSuccessResponse($data, 'Information fetched Successfully!');
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
