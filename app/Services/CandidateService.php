<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\RecentJoinCandidateResource;
use App\Http\Resources\SearchResource;
use App\Models\Generic;
use App\Models\Occupation;
use App\Models\Religion;
use App\Models\StudyLevel;
use App\Models\TeamConnection;
use App\Models\User;
use App\Models\CandidateImage;
use App\Models\CandidateInformation;
use App\Repositories\CandidateImageRepository;
use App\Repositories\CountryRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Repositories\CandidateRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;
use App\Transformers\CandidateTransformer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response as FResponse;
use App\Repositories\RepresentativeInformationRepository as RepresentativeRepository;
use function PHPUnit\Framework\throwException;


class CandidateService extends ApiBaseService
{

    use CrudTrait;

    const INFORMATION_FETCHED_SUCCESSFULLY = 'Information fetched Successfully!';
    const INFORMATION_UPDATED_SUCCESSFULLY = 'Information updated Successfully!';
    const IMAGE_DELETED_SUCCESSFULLY = 'Image Deleted successfully!';

    /**
     * @var CandidateRepository
     */
    protected $candidateRepository;

    /**
     * @var CandidateRepository
     */
    protected $imageRepository;

    /**
     * @var CandidateTransformer
     */
    protected $candidateTransformer;

    /**
     * CandidateService constructor.
     *
     * @param CandidateRepository $candidateRepository
     */

    /**
     * @var BlockListService
     */
    protected $blockListService;


    /**
     * @var RepresentativeRepository
     */
    protected $representativeRepository;
    /**
     * @var CountryRepository
     */
    private $countryRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(
        CandidateRepository $candidateRepository,
        CandidateImageRepository $imageRepository,
        CandidateTransformer $candidateTransformer,
        BlockListService $blockListService,
        RepresentativeRepository $representativeRepository,
        CountryRepository $countryRepository,
        UserRepository $userRepository
    )
    {
        $this->candidateRepository = $candidateRepository;
        $this->imageRepository = $imageRepository;
        $this->candidateTransformer = $candidateTransformer;
        $this->blockListService = $blockListService;
        $this->representativeRepository = $representativeRepository;
        $this->setActionRepository($candidateRepository);
        $this->countryRepository = $countryRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function store($request)
    {
        try {
            $userId = self::getUserId();
            $checkCandidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if ($checkCandidate) {
                return $this->sendSuccessResponse($checkCandidate, 'Candidate Information Already Exists', [], HttpStatusCode::SUCCESS);
            }
            $request['user_id'] = $userId;
            $candidate = $this->candidateRepository->save($request);
            if ($candidate) {
                $userInfo = User::find($userId);
                if ($userInfo) {
                    $userInfo->full_name = trim($request['first_name']) . ' ' . trim($request['last_name']);
                    $userInfo->save();
                }
                return $this->sendSuccessResponse($candidate, 'Information save Successfully!', [], HttpStatusCode::CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * fetch candidate all info
     * @param int $userId
     * @return JsonResponse
     */

    public function fetchCandidateInfo(int $userId): JsonResponse
    {
        $candidate = $this->candidateRepository->findOneByProperties([
            'user_id' => $userId
        ]);
        if (!$candidate) {
            throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
        }
        $images = $this->imageRepository->findBy(['user_id'=>$userId]);
        $candidate_info = $this->candidateTransformer->transform($candidate);
        $candidate_info['essential'] = $this->candidateTransformer->transformPersonalEssential($candidate)['essential'];
        $candidate_other_image = $candidate->other_images;
        /* Find Team Connection Status (We Decline or They Decline )*/

        $status['connectionRequestSendType'] = null;
        $status['teamConnectStatus'] = null;

        $candidateTeam = $candidate->active_team ;

        if($candidateTeam){
            $activeTeam = Auth::user()->getCandidate->active_team;

            $connectFrom = $activeTeam->sentRequest->pluck('team_id')->toArray();
            $connectTo = $activeTeam->receivedRequest->pluck('team_id')->toArray();

            $teamId = $candidate->active_team->id;

            if(in_array($candidate->active_team->team_id,$connectFrom)){
                $status['connectionRequestSendType'] = 1;
                $teamConnectStatus = TeamConnection::where('from_team_id',$activeTeam->id)->where('to_team_id',$teamId)->first();
                $status['teamConnectStatus'] = $teamConnectStatus ? $teamConnectStatus->connection_status : null;
            }elseif (in_array($candidate->active_team->team_id,$connectTo)){
                $status['connectionRequestSendType'] = 2;
                $teamConnectStatus = TeamConnection::where('from_team_id',$teamId)->where('from_team_id',$activeTeam->id)->first();
                $status['teamConnectStatus'] = $teamConnectStatus ? $teamConnectStatus->connection_status : null;
            }else{
                $status['connectionRequestSendType'] = null;
                $status['teamConnectStatus'] = null;
            }
        }

        $candidate_details = array_merge(
            $candidate_info,
            [
                'essential' => $this->candidateTransformer->transformPersonal($candidate)['essential'],
            ],
            [
                'general' => $this->candidateTransformer->transformPersonal($candidate)['general'],
            ],
            [
                'contact' => $this->candidateTransformer->transformPersonal($candidate)['contact'],
            ],
            [
                'more_about' =>  $this->candidateTransformer->transformPersonal($candidate)['more_about'],
            ],
            [
                'other_images' => $candidate_other_image
            ],
            [
                'status' => $status
            ],
        );
        return $this->sendSuccessResponse($candidate_details, self::INFORMATION_FETCHED_SUCCESSFULLY);
    }

    /**
     * fetch resource
     * @return JsonResponse
     */
    public function fetchCandidatePersonalInfo(): JsonResponse
    {
        $userId = self::getUserId();
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
            $personal_info = $this->candidateTransformer->transformPersonal($candidate);
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_FETCHED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Update resource
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function candidateBasicInfoStore(Request $request, int $userId): JsonResponse
    {
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
            $input = $request->all(CandidateInformation::BASIC_INFO);

            // As BaseRepository update method has bug that's why we have to fallback to model default methods.
            $input = $candidate->fill($input)->toArray();
            $candidate->save($input);
            $personal_info = $this->candidateTransformer->transformPersonalBasic($candidate);
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * fetch resource
     * @return JsonResponse
     */
    public function fetchProfileInitialInfo(): JsonResponse
    {       
        $userId = self::getUserId();
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {                
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
           // dd($candidate);
            $data['user'] = $this->candidateTransformer->transform($candidate);
            $data['personal_info'] = $this->candidateTransformer->transformPersonal($candidate);
            $data['countries'] = $this->countryRepository->findAll()->where('status', '=', 1);
            $data['studylevels'] = StudyLevel::orderBy('id')->get();
            $data['religions'] = Religion::where('status', 1)->orderBy('name')->get();
            $data['occupations'] = Occupation::all();
            $data['validation_info'] = $this->candidateTransformer->transformPersonalVerification($candidate);;
            $images = $this->imageRepository->findBy(['user_id'=>$userId]);
            $images = $this->candidateTransformer->candidateOtherImage($images,true);
//            for ($i = 0; $i < count($images); $i++) {
//                $images[$i]->image_path = $images[$i]->image_path ? env('IMAGE_SERVER') .'/'. $images[$i]->image_path : '';
//            }

            //$data['candidate_image']["avatar_image_url"] = $candidate->per_avatar_url? env('IMAGE_SERVER') .'/'. $candidate->per_avatar_url : '';
            $data['candidate_image']["avatar_image_url"] = isset($candidate->per_avatar_url) ? $candidate->per_avatar_url : '';

            //$data['candidate_image']["main_image_url"] = $candidate->per_main_image_url ? env('IMAGE_SERVER') .'/'. $candidate->per_main_image_url : '';
            $data['candidate_image']["main_image_url"] = isset($candidate->per_main_image_url) ?  $candidate->per_main_image_url : '';
            $data['candidate_image']["other_images"] = $images;

            return $this->sendSuccessResponse($data, self::INFORMATION_FETCHED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * @return \App\Services\JsonResponse|\Illuminate\Http\Response
     */
    public function candidateStatus()
    {
        $userId = self::getUserId();

        try{
            $authUser = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$authUser) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }

            $activeTeam = $authUser->active_team;

            if (!$activeTeam) {
                throw new Exception('Team not found, Please create team first');
            }
            $candidates = $this->candidateRepository->getModel();

            /* FILTER - by candidate block list  */
            $userInfo['blockList'] = $authUser->blockList->pluck('user_id')->toArray();

            /* FILTER - Own along with team member and block list candidate  */
            $activeTeamUserIds = $activeTeam->team_members->pluck('user_id')->toArray();

            /* FILTER - Remove Team users already in connected list (pending, connected or rejected)  */
            $connectFromMembersId = $activeTeam->sentRequestMembers->pluck('user_id')->toArray();
            $connectToMembersId = $activeTeam->receivedRequestMembers->pluck('user_id')->toArray();

            /* FILTER - Gender  */
            $gender = $authUser->gender == 1 ? 2 : 1 ;

            /* FILTER - Age  */
            $dateRange['max'] = Carbon::now()->subYears($authUser->max_age);
            $dateRange['min'] = Carbon::now()->subYears($authUser->mim_age);

            /* FILTER - Height  */
            $heightRange['min'] = $authUser->min_height;
            $heightRange['max'] = $authUser->max_height;

            /* FILTER - Ethnicity  */
            $ethnicity = $authUser->ethnicity;
            $exceptIds = array_unique(array_merge($userInfo['blockList'],$activeTeamUserIds,$connectFromMembersId,$connectToMembersId));
            $filter = $candidates->with('user')
                ->where('data_input_status','>',2)
                ->whereNotIn('user_id',$exceptIds)
                ->whereNotIn('per_current_residence_country',$authUser->bloked_countries->pluck('id')->toArray())
                ->where('per_gender', $gender)
                ->whereBetween('dob', [$dateRange])
                ->whereBetween('per_height', [$heightRange])
                ->where('per_ethnicity', $ethnicity);

            $result['suggestion'] = $filter->count();
            $result['newSuggestion'] = $filter->whereHas('user', function($q){
                $q->where('created_at','>', Carbon::now()->subDays(3)); // User Register within 3 days
            })->count();

            $result['teamListed'] = $activeTeam->teamListedUser->count();
            $result['shortListed'] = $authUser->shortList->count();
            $connectFromCount = $activeTeam->sentRequest->count();
            $connectToCount = $activeTeam->receivedRequest->count();
            $result['connected'] = $connectFromCount + $connectToCount;
            $result['requestReceive'] = $connectFromCount;
            $result['requestSend'] = $connectToCount;

            return $this->sendSuccessResponse($result, "Candidates Status fetched successfully");

        }catch (Exception $exception){
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Update resource
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function candidatePersonalInfoUpdate(Request $request, int $userId): JsonResponse
    {
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
            $input = $request->all(CandidateInformation::PERSONAL_INFO);

            // As BaseRepository update method has bug that's why we have to fallback to model default methods.
            $input = $candidate->fill($input)->toArray();
            $candidate->save($input);
            $personal_info = $this->candidateTransformer->transformPersonal($candidate);
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Update resource
     * @param Request $request
     * @return JsonResponse
     */
    public function candidateEssentialPersonalInfoUpdate(Request $request): JsonResponse
    {
        $userId = self::getUserId();
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }

            $candidate->dob = $request->input('dob');
            $candidate->per_gender = $request->input('per_gender');
            $candidate->per_height = $request->input('per_height');
            $candidate->per_employment_status = $request->input('per_employment_status');
            $candidate->per_education_level_id = $request->input('per_education_level_id');
            $candidate->per_religion_id = $request->input('per_religion_id');
            $candidate->per_occupation = $request->input('per_occupation');


            $candidate->save();
            $personal_info = $this->candidateTransformer->transformPersonalEssential($candidate);
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Update resource
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function candidatePersonalGeneralInfoUpdate(Request $request): JsonResponse
    {
        $userId = self::getUserId();
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
            $input = $request->all(CandidateInformation::PERSONAL_GENERAL_INFO);

            // As BaseRepository update method has bug that's why we have to fallback to model default methods.
            $input = $candidate->fill($input)->toArray();
            $candidate->save($input);
            $personal_info = $this->candidateTransformer->transformPersonalGeneral($candidate);
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Update resource
     * @param Request $request
     * @return JsonResponse
     */
    public function candidatePersonalContactInfoUpdate(Request $request): JsonResponse
    {
        $userId = self::getUserId();
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
            $input = $request->all(CandidateInformation::PERSONAL_CONTACT_INFO);

            $input = $candidate->fill($input)->toArray();
            $candidate->save($input);
            $personal_info = $this->candidateTransformer->transformPersonalContact($candidate);
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Update resource
     * @param Request $request
     * @return JsonResponse
     */
    public function candidatePersonalMoreAboutInfoUpdate(Request $request): JsonResponse
    {
        $userId = self::getUserId();
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
            $input = $request->only(CandidateInformation::PERSONAL_MOREABOUT_INFO);

            $input['per_improve_myself'] = json_encode($request->per_improve_myself);

            if($request->hasFile('per_additional_info_doc')){
                $candidateFile = $this->uploadImageThrowGuzzle([
                    'per_additional_info_doc'=>$request->file('per_additional_info_doc'),
                ]);
                $input['per_additional_info_doc'] = $candidateFile->per_additional_info_doc;
            }

            $input = $candidate->fill($input)->toArray();
            $candidate->save($input);
            $personal_info = $this->candidateTransformer->transformPersonalMoreAbout($candidate);
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * fetch resource
     * @param int $userId
     * @return JsonResponse
     */
    public function fetchPreferenceInfo(int $userId): JsonResponse
    {
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
            $personal_info = $this->candidateTransformer->transformPreference($candidate);
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_FETCHED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Update resource
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function storePreferenceInfo(Request $request): JsonResponse
    {
        try {
            $userId = self::getUserId();
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
            $input = $request->only(CandidateInformation::PREFERENCE_INFO);
            $input = $candidate->fill($input)->toArray();
            DB::beginTransaction();
            $candidate->save($input);

            if ($request->has('pre_has_country_allow_preference')) {
                if ($request->pre_has_country_allow_preference) {
                    $candidate->preferred_countries()->sync($request->pre_countries);
                    $candidate->preferred_cities()->sync($request->pre_cities);
                } else {
                    $candidate->preferred_countries()->detach();
                    $candidate->preferred_cities()->detach();
                }
            }

            if ($request->has('pre_has_country_disallow_preference')) {
                if ($request->pre_has_country_disallow_preference) {
                    $candidate->bloked_countries()->sync(array_fill_keys($request->pre_disallow_countries, ['allow' => 0]));
                    $candidate->blocked_cities()->sync(array_fill_keys($request->pre_disallow_cities, ['allow' => 0]));
                } else {
                    $candidate->bloked_countries()->detach();
                    $candidate->blocked_cities()->detach();
                }
            }

            if ($request->has('pre_nationality')) {
                $candidate->preferred_nationality()->sync($request->pre_nationality);
            }
            $personal_info = $this->candidateTransformer->transformPreference($candidate);
            DB::commit();
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storePreferenceAbout(Request $request): JsonResponse
    {
        try {
            $userId = self::getUserId();
            $candidate = $this->candidateRepository->findOneByProperties(['user_id' => $userId]);
            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }

            $candidate->pre_partner_age_min = $request->input('pre_partner_age_min');
            $candidate->pre_partner_age_max = $request->input('pre_partner_age_max');
            $candidate->pre_height_min = $request->input('pre_height_min');
            $candidate->pre_height_max = $request->input('pre_height_max');
            $candidate->pre_has_country_allow_preference = $request->input('pre_has_country_allow_preference');
            $candidate->pre_has_country_disallow_preference = $request->input('pre_has_country_disallow_preference');
            $candidate->pre_partner_religions = $request->input('pre_partner_religions');
            $candidate->pre_ethnicities = $request->input('pre_ethnicities');
            $candidate->pre_study_level_id = $request->input('pre_study_level_id');
            $candidate->pre_employment_status = $request->input('pre_employment_status');
            $candidate->pre_occupation = $request->input('pre_occupation');
            $candidate->pre_preferred_divorcee = $request->input('pre_preferred_divorcee');
            $candidate->pre_preferred_divorcee_child = $request->input('pre_preferred_divorcee_child');
            $candidate->pre_other_preference = $request->input('pre_other_preference');
            $candidate->pre_description = $request->input('pre_description');

            DB::beginTransaction();
            $candidate->save();

            if ($request->has('pre_has_country_allow_preference') && count($request->pre_partner_comes_from) > 0) {
                $country = [];
                $city = [];
                foreach ($request->pre_partner_comes_from as $key => $county) {
                    $country[] = ['candidate_pre_country_id' => $county['country'], 'candidate_pre_city_id' => isset($county['city']) ? $county['city'] : null];
                    /* avoid city with null or 0 value */
                    if (isset($county['city'])) {
                        $city[] = ['city_id' => $county['city'], 'country_id' => $county['country']];
                    }
                }
                if ($request->pre_has_country_allow_preference) {
                    if (count($country) > 0):
                        $candidate->preferred_countries()->detach();
                        $candidate->preferred_countries()->sync($country);
                    endif;
                    if (count($city) > 0):
                        $candidate->preferred_cities()->detach();
                        $candidate->preferred_cities()->sync($city);
                    endif;
                } else {
                    $candidate->preferred_countries()->detach();
                    $candidate->preferred_cities()->detach();
                }
            }

            if ($request->has('pre_has_country_disallow_preference') && count($request->pre_disallow_preference) > 0) {
                $bcountry = [];
                $bcity = [];
                foreach ($request->pre_disallow_preference as $key => $bcounty) {

                    $bcountry[] = ['candidate_pre_country_id' => $bcounty['country'], 'candidate_pre_city_id' => $bcounty['city'], 'allow' => '0'];

                    /* avoid city with null or 0 value */
                    if (isset($bcounty['city'])) {
                        $bcity[] = ['city_id' => $bcounty['city'], 'country_id' => $bcounty['country'], 'allow' => 0];
                    }

                }
                if ($request->pre_has_country_disallow_preference) {
                    if (count($bcountry) > 0):
                        $candidate->bloked_countries()->detach();
                        $candidate->bloked_countries()->sync($bcountry);
                    endif;
                    if (count($bcity) > 0):
                        $candidate->blocked_cities()->detach();
                        $candidate->blocked_cities()->sync($bcity);
                    endif;
                } else {
                    $candidate->bloked_countries()->detach();
                    $candidate->blocked_cities()->detach();
                }
            }

            if ($request->has('pre_nationality')) {
                $candidate->preferred_nationality()->sync($request->pre_nationality);
            }
            $personal_info = $this->candidateTransformer->transformPreference($candidate);
            DB::commit();
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storePreferenceRate(Request $request): JsonResponse
    {
        try {
            $userId = self::getUserId();
            $candidate = $this->candidateRepository->findOneByProperties(['user_id' => $userId]);
            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }

            $candidate->pre_strength_of_character_rate = $request->input('pre_strength_of_character_rate') ?? 0;
            $candidate->pre_look_and_appearance_rate = $request->input('pre_look_and_appearance_rate') ?? 0;
            $candidate->pre_religiosity_or_faith_rate = $request->input('pre_religiosity_or_faith_rate') ?? 0;
            $candidate->pre_manners_socialskill_ethics_rate = $request->input('pre_manners_socialskill_ethics_rate') ?? 0;
            $candidate->pre_emotional_maturity_rate = $request->input('pre_emotional_maturity_rate') ?? 0;
            $candidate->pre_good_listener_rate = $request->input('pre_good_listener_rate') ?? 0;
            $candidate->pre_good_talker_rate = $request->input('pre_good_talker_rate') ?? 0;
            $candidate->pre_wiling_to_learn_rate = $request->input('pre_wiling_to_learn_rate') ?? 0;
            $candidate->pre_family_social_status_rate = $request->input('pre_family_social_status_rate') ?? 0;
            $candidate->pre_employment_wealth_rate = $request->input('pre_employment_wealth_rate') ?? 0;
            $candidate->pre_education_rate = $request->input('pre_education_rate') ?? 0;

            DB::beginTransaction();
            $candidate->save();

            $personal_info = $this->candidateTransformer->transformPreference($candidate);
            DB::commit();
            return $this->sendSuccessResponse($personal_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * this function use for getting candidate family informations
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function candidateFamilyInfoList($request)
    {
        try {
            $uid = $request->get('uid');
            $candidateinfo = $this->candidateRepository->findOneByProperties([
                'user_id' => $uid
            ]);
            if (!empty($candidateinfo)) {
                $responseInfo = array();
                $responseInfo["user_id"] = $candidateinfo->user_id;
                $responseInfo["fi_father_name"] = $candidateinfo->fi_father_name;
                $responseInfo["fi_father_profession"] = $candidateinfo->fi_father_profession;
                $responseInfo["fi_mother_name"] = $candidateinfo->fi_mother_name;
                $responseInfo["fi_mother_profession"] = $candidateinfo->fi_mother_profession;
                $responseInfo["fi_siblings_desc"] = $candidateinfo->fi_siblings_desc;
                $responseInfo["fi_country_of_origin"] = $candidateinfo->fi_country_of_origin;
                $responseInfo["fi_family_info"] = $candidateinfo->fi_family_info;
                return $this->sendSuccessResponse($responseInfo, 'Family Info listed successfully');
            } else {
                return $this->sendErrorResponse('Invalid User ID', ['detail' => 'User ID Not found'],
                    HttpStatusCode::BAD_REQUEST
                );
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => HttpStatusCode::NOT_FOUND,
                'message' => $e->getMessage(),
                'error' => ['details' => $e->getMessage()]
            ], HttpStatusCode::NOT_FOUND);
        }
    }

    /**
     * this function use for updating candidate family informations
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function candidateFamilyInfoUpdate($request)
    {
        try {
            $uid = $request->get('uid');
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $uid
            ]);
            if (!empty($candidate)) {
                // Update family info
                $candidate->fi_father_name = $request->get('father_name');
                $candidate->fi_father_profession = $request->get('father_profession');
                $candidate->fi_mother_name = $request->get('mother_name');
                $candidate->fi_mother_profession = $request->get('mother_profession');
                $candidate->fi_siblings_desc = $request->get('siblings_desc');
                $candidate->fi_country_of_origin = $request->get('country_of_origin');
                $candidate->fi_family_info = $request->get('family_info');
                $candidate->timestamps = false;
                $candidate->save();

                return $this->sendSuccessResponse($candidate, 'Family Info updated successfully');
            } else {
                return $this->sendErrorResponse('Invalid User ID', ['detail' => 'User ID Not found'],
                    HttpStatusCode::BAD_REQUEST
                );
            }


        } catch (Exception $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => HttpStatusCode::NOT_FOUND,
                'message' => $e->getMessage(),
                'error' => ['details' => $e->getMessage()]
            ], HttpStatusCode::NOT_FOUND);
        }
    }

    /**
     * this function is used for candidate personal validation information
     * @return JsonResponse
     */
    public function getVerificationInfo(): JsonResponse
    {
        $userId = self::getUserId();
        $candidate = $this->candidateRepository->findOneByProperties([
            'user_id' => $userId
        ]);
        if (!$candidate) {
            throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
        }

        $candidate_verification_info = $this->candidateTransformer->transformPersonalVerification($candidate);
        return $this->sendSuccessResponse($candidate_verification_info, self::INFORMATION_FETCHED_SUCCESSFULLY);
    }

    /**
     * this function is used for store candidate personal validation information
     * @return JsonResponse
     */
    public function updateVerificationInfo(Request $request): JsonResponse
    {
        $userId = self::getUserId();
        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }

            DB::beginTransaction();

            $input = $request->only(CandidateInformation::PERSONAL_VERIFICATION_INFO);

            if($request->hasFile('ver_image_front')){
                $image = $this->uploadImageThrowGuzzle([
                    'ver_image_front'=>$request->file('ver_image_front'),
                ]);
                $input['ver_image_front'] = $image->ver_image_front;
            }

            if($request->hasFile('ver_image_back')){
                $image = $this->uploadImageThrowGuzzle([
                    'ver_image_back'=>$request->file('ver_image_back')
                ]);
                $input['ver_image_back'] = $image->ver_image_back ;
            }

            $input = $candidate->fill($input)->toArray();

            $candidate->save($input);

            $candidate_verification_info = $this->candidateTransformer->transformPersonalVerification($candidate);

            DB::commit();
            return $this->sendSuccessResponse($candidate_verification_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * This function is for update candidate info status ( DB field candidate_information.data_input_status ) update
     * @param Request $request
     * @return JsonResponse
     */
    public function updateInfoStatus(Request $request): JsonResponse
    {
        $userId = self::getUserId();

        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }
            DB::beginTransaction();
            $info['data_input_status'] = $request->data_input_status;
            $candidate->update($info);

            $candidate_basic_info = $this->candidateTransformer->transformPersonalBasic($candidate);
            DB::commit();
            return $this->sendSuccessResponse($candidate_basic_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * @return JsonResponse
     */
    public function listImage(): JsonResponse
    {
        try {
            $userId = self::getUserId();
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            $avatar_image_url = $candidate->per_avatar_url;
            $main_image_url = $candidate->per_main_image_url;
            $other_images = $candidate->other_images;
//            $images = $this->imageRepository->findBy(['user_id'=>$userId]);
//            $images = $this->candidateTransformer->candidateOtherImage($images,true);
//            for ($i = 0; $i < count($images); $i++) {
//                $images[$i]->image_path = $images[$i]->image_path ? env('IMAGE_SERVER') .'/'. $images[$i]->image_path : '';
//            }

            $data = array();
            // $data["avatar_image_url"] = $avatar_image_url ? env('IMAGE_SERVER') .'/'. $avatar_image_url : '';
            // $data["main_image_url"] = $main_image_url ? env('IMAGE_SERVER') .'/'. $main_image_url : '';

            $data["avatar_image_url"] = isset($avatar_image_url) ? $avatar_image_url : '';
            $data["main_image_url"] = isset($main_image_url) ?  $main_image_url : '';

            $data["other_images"] = isset($other_images) ? $other_images : '';


            return $this->sendSuccessResponse($data, self::INFORMATION_FETCHED_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param array $input
     * @return JsonResponse
     */
    public function uploadImage(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $userId = self::getUserId();
            $checkRepresentative = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$checkRepresentative) {
                return $this->sendErrorResponse('Candidate information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }

            if ($request->hasFile('per_avatar_url')) {
                $image = $this->uploadImageThrowGuzzle([
                    'per_avatar_url'=>$request->file('per_avatar_url'),
                ]);
                $checkRepresentative->per_avatar_url = $image->per_avatar_url;
            }

            if ($request->hasFile('per_main_image_url')) {
                $image = $this->uploadImageThrowGuzzle([
                    'per_main_image_url'=>$request->file('per_main_image_url'),
                ]);
                $checkRepresentative->per_main_image_url = $image->per_main_image_url;
            }

            if ($request->hasFile('other_images')) {
                $image = $this->uploadImageThrowGuzzle([
                    'other_images'=>$request->file('other_images'),
                ]);

                $checkRepresentative->other_images = $image->other_images;
            }

            if (isset($request['anybody_can_see'])) {
                $checkRepresentative->anybody_can_see = $request['anybody_can_see'];
            }
            if (isset($request['only_team_can_see'])) {
                $checkRepresentative->only_team_can_see = $request['only_team_can_see'];
            }
            if (isset($request['team_connection_can_see'])) {
                $checkRepresentative->team_connection_can_see = $request['team_connection_can_see'];
            }
            $checkRepresentative->save();


           // $searchCriteria = ["user_id" => $checkRepresentative->user_id];
            $avatar_image_url = $checkRepresentative->per_avatar_url;
            $main_image_url = $checkRepresentative->per_main_image_url;
            $other_images = $checkRepresentative->other_images;

//            $images = $this->imageRepository->findBy($searchCriteria);

//            $images = $this->candidateTransformer->candidateOtherImage($images,true);


            $data = array();

            $data["avatar_image_url"] = $avatar_image_url ?? '';
            $data["main_image_url"] = $main_image_url ?? '';

            $data["other_images"] = $other_images ?? '';

            DB::commit();
//            $checkRepresentative->per_avatar_url = (!empty($checkRepresentative->per_avatar_url) ? 'api.arranzed.com/api' . $checkRepresentative->per_avatar_url : '');
            return $this->sendSuccessResponse($data, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param array $input
     * @param CandidateImage $candidateImage
     * @return JsonResponse
     */
    public function updateImage(Request $request): JsonResponse
    {

        try {
            DB::beginTransaction();
            $userId = self::getUserId();

            $checkRepresentative = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$checkRepresentative) {
                return $this->sendErrorResponse('Candidate information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            if ($request->hasFile('per_avatar_url')) {
                $per_avatar_url = $this->singleImageUploadFile($request->file('per_avatar_url'));
                $checkRepresentative->per_avatar_url = $per_avatar_url['image_path'];
            }
            if ($request->hasFile('per_main_image_url')) {
                $per_main_image_url = $this->singleImageUploadFile($request->file('per_main_image_url'));
                $checkRepresentative->per_main_image_url = $per_main_image_url['image_path'];
            }
            if (!empty($request['anybody_can_see'])) {
                $checkRepresentative->anybody_can_see = $request['anybody_can_see'];
            }
            if (!empty($request['only_team_can_see'])) {
                $checkRepresentative->only_team_can_see = $request['only_team_can_see'];
            }
            if (!empty($request['team_connection_can_see'])) {
                $checkRepresentative->team_connection_can_see = $request['team_connection_can_see'];
            }
            $checkRepresentative->save();

            if (isset($request['image']) && count($request['image']) > 0) {
                foreach ($request['image'] as $key => $file) {
                    $imageInfo = $this->imageRepository->findOneByProperties([
                        'user_id' => $userId,
                        'id' => $file['id']
                    ]);

                    $requestFile = $request->file("image.$key.image");
                    $requestFileType = $file['type'];
                    $input = $this->singleImageUploadFile($requestFile, $requestFileType);
                    $imageInfo->image_type = $requestFileType;
                    $imageInfo->image_path = $input['image_path'];
                    $imageInfo->disk = $input['disk'];
                    $imageInfo->image_visibility = $file['visibility'];
//                $this->deleteFile($imageInfo['image_path']);

                    $imageInfo->save();
                }
            }
            DB::commit();
            return $this->sendSuccessResponse($checkRepresentative, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param CandidateImage $candidateImage
     * @return JsonResponse
     */
    public function deleteImage(CandidateImage $candidateImage): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->deleteFile($candidateImage);
            $candidateImage->delete();
            DB::commit();
            return $this->sendSuccessResponse([], self::IMAGE_DELETED_SUCCESSFULLY);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param int $imageType
     * @return JsonResponse
     */
    public function deleteImageByType(int $imageType): JsonResponse
    {
        $userId = self::getUserId();

        if ($imageType >= 10) {
            return $this->sendErrorResponse(
                HttpStatusCode::VALIDATION_ERROR, HttpStatusCode::VALIDATION_ERROR_MESSAGE
            );
        }

        try {

            if($imageType == 0 || $imageType == 1){

                $candidate = $this->candidateRepository->findOneByProperties([
                    'user_id' => $userId
                ]);


                if($imageType == 0){
                    $candidate->per_avatar_url = null ;
                    $candidate->save();
                }

                if ($imageType === 1){
                    $candidate->per_main_image_url = null ;
                    $candidate->save();
                }

                if ($imageType === 9) {
                    $candidate->other_images = null ;
                    $candidate->save();
                }

            }elseif (in_array((int)$image_type,[2,3,4,5,6,7,8])){
                $imageInfo = $this->imageRepository->findOneByProperties([
                    'user_id' => $userId,
                    'image_type' => $image_type
                ]);
                $imageInfo->delete();
            }

            /* edo Need to remove from image server  */

            return $this->sendSuccessResponse([], self::IMAGE_DELETED_SUCCESSFULLY);

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param CandidateImage $candidateImage
     * @return bool
     * @throws Exception
     */
    private function deleteFile(CandidateImage $candidateImage): bool
    {
        if (!Storage::disk($candidateImage->disk)->exists($candidateImage->image_path)) {
            throw new NotFoundHttpException("Image not found in $candidateImage->disk disk");
        }
        $file_delete_status = Storage::disk($candidateImage->disk)->delete($candidateImage->image_path);
        if (!$file_delete_status) {
            throw new Exception('File can\'t be deleted!');
        }
        return $file_delete_status;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function uploadFile(Request $request): array
    {
        $requestFile = $request->file('image');
        $file = 'user-' . $request->user()->id;
        $image_type = CandidateImage::getImageType($request->{CandidateImage::IMAGE_TYPE});
        $disk = config('filesystems.default', 'local');
        $status = $requestFile->storeAs($file, $image_type . '-' . $requestFile->getClientOriginalName(), $disk);
        return [
            CandidateImage::IMAGE_PATH => $status,
            CandidateImage::IMAGE_DISK => $disk
        ];

    }


    /**
     * @param Request $request
     * @return array
     */
    private function singleImageUploadFile($requestFile, $imageType = null)
    {
        $userId = self::getUserId();
        $image_type = $imageType ?: 'gallery'; //CandidateImage::getImageType($imageType);
        $file = 'candidate-' . $userId;
        $disk = config('filesystems.default', 'local');
        $status = $requestFile->storeAs($file, $image_type . '-' . $requestFile->getClientOriginalName(), $disk); // storeAs(PATH,NAME,OPTION)
        return [
            CandidateImage::IMAGE_PATH => asset('/images/'.$status),
            CandidateImage::IMAGE_DISK => $disk
        ];

    }

    /**
     * Write code on Construct
     *
     * @return \Illuminate\Http\Response
     */
    public function removeImage($imageName)
    {
        if (Storage::exists(public_path($imageName))) {
            Storage::delete(public_path($imageName));
            return 200;
        } else {
            return 500;
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCandidateGallery(Request $request)
    {
        $user_id = $request->user_id;
        if (!$user_id) {
            $user_id = Auth::id();
        }

        $candidate = $this->candidateRepository->findOneByProperties([
            "user_id" => $user_id
        ]);

        if (!$candidate) {
            return $this->sendErrorResponse('Candidate not found.', [], HttpStatusCode::NOT_FOUND);
        }

        $searchCriteria = ["user_id" => $user_id];
        $avatar_image_url = $candidate->per_avatar_url;

        $images = $this->imageRepository->findBy($searchCriteria);

        $images = $this->candidateTransformer->candidateOtherImage($images,CandidateImage::getPermissionStatus($user_id));

        $data = array();
        // $data["avatar_image_url"] = $avatar_image_url ? env('IMAGE_SERVER') .'/'. $avatar_image_url : '';
        $data["avatar_image_url"] = isset($avatar_image_url) ? $avatar_image_url : '';
        $data["main_image_url"] = CandidateImage::getCandidateMainImage($user_id);
        $data["other_images"] = $images;

        return $this->sendSuccessResponse($data, self::INFORMATION_FETCHED_SUCCESSFULLY);
    }

    /**
     * @return JsonResponse
     */
    public function reccentJoinCandidate()
    {
        //only candidate can be seen
        //only verified candidate
        //payed candidate(hard logic)
        //register complete (soft hard)
        //if registered they will show here
        //need to  have logic if traffic is low.
//        $shortListedCandidates = $this->candidateRepository->findBy([
//            'data_input_status' => 1,
//            'per_page' => 3,
//        ], null, ['column' => 'id', 'direction' => 'desc']);

        $recentJoinUsers = $this->userRepository->getModel()->with('getCandidate')->whereHas('getCandidate',function($q){
            $q->where('data_input_status','>',1);
        })->latest()->limit(12)->get();

        $shortListedCandidates = [];
        foreach ($recentJoinUsers as $user){
            $candidate = $user->getCandidate;
            $candidate->per_main_image_url = $candidate->per_avatar_url;
            $shortListedCandidates[] = $candidate;
        }

        $formatted_data = RecentJoinCandidateResource::collection($shortListedCandidates);

        return $this->sendSuccessResponse($formatted_data, 'Recently join candidate List');
    }

    /**
     * @return JsonResponse
     */
    public function suggestions()
    {
        $userId = self::getUserId();
        $userInfo = Self::getUserInfo();
        $parpage = 10;
        $search = $this->actionRepository->getModel()->newQuery();

        // check block listed user
        if (!empty($userId)) {
            $blockUser = array();
            $silgleBLockList = $this->blockListService->blockListByUser($userId);
            if (count($silgleBLockList) >= 1) {
                $blockUser = $silgleBLockList;
            }
            $teamBlockList = $this->blockListService->getTeamBlockListByUser($userId);

            if (!empty($teamBlockList) && count($teamBlockList) >= 1) {
                if (count($silgleBLockList) >= 1) {
                    $combineBlockUser = array_merge($silgleBLockList->toArray(), $teamBlockList->toArray());

                } else {
                    $combineBlockUser = $teamBlockList;
                }
                $search->whereNotIn('user_id', $combineBlockUser);
            }

        }
        // Check user status
        $search->join('users', function ($join) {
            $join->on('users.id', '=', 'candidate_information.user_id')
                ->where('status', '=', 1);
        });

        if ($userInfo['account_type'] == 1):
            $candidateInfo = $this->candidateRepository->findOneBy(['user_id' => $userId]);
            if (!empty($candidateInfo)):

                $minAge = (!empty($candidateInfo['pre_partner_age_min'])) ? Carbon::now()->subYear($candidateInfo['pre_partner_age_min'])->format('Y-m-d') : Carbon::now()->subYear(16)->format('Y-m-d');
                $maxAge = (isset($candidateInfo['pre_partner_age_max']) && !empty($candidateInfo['pre_partner_age_max'])) ? Carbon::now()->subYear($candidateInfo['pre_partner_age_max'])->format('Y-m-d') : Carbon::now()->subYear(40)->format('Y-m-d');
                $minHeight = (isset($candidateInfo['pre_height_min']) && !empty($candidateInfo['pre_height_min']) && $candidateInfo['pre_height_min'] > 3) ? $candidateInfo['pre_height_min'] : 3;
                $maxHeight = (isset($candidateInfo['pre_height_max']) && !empty($candidateInfo['pre_height_max']) && $candidateInfo['pre_height_max'] > 3) ? $candidateInfo['pre_height_max'] : 8;


                $search->whereBetween('dob', [$maxAge, $minAge]);
                $search->whereBetween('per_height', [$minHeight, $maxHeight]);


                // pre_preferred_divorcee
//            if (isset($candidateInfo['pre_preferred_divorcee']) and !empty($candidateInfo['pre_preferred_divorcee'])) {
//                $pre_preferred_divorcee = $candidateInfo['pre_preferred_divorcee'];
//                $search->where('per_mother_tongue', '=', $pre_preferred_divorcee);
//            }

                // Religion
                if (isset($candidateInfo['pre_partner_religions']) and !empty($candidateInfo['pre_partner_religions'])) {
                    $religion = explode(',', $candidateInfo['pre_partner_religions']);
                    $search->whereIn('per_religion_id', $religion);
                }
                //  ethnicity
                if (isset($candidateInfo['pre_ethnicities']) and !empty($candidateInfo['pre_ethnicities'])) {
                    $per_ethnicity = $candidateInfo['pre_ethnicities'];
                    $search->orWhere('per_ethnicity', 'like', '%' . $per_ethnicity . '%');
                }
                // per_marital_status
                if (isset($candidateInfo['marital_status']) and !empty($candidateInfo['marital_status'])) {
                    $per_marital_status = $candidateInfo['marital_status'];
                    $search->where('per_marital_status', '=', $per_marital_status);
                }

                // per_occupation
                if (isset($candidateInfo['pre_occupation']) and !empty($candidateInfo['pre_occupation'])) {
                    $per_occupation = $candidateInfo['pre_occupation'];
                    $search->orWhere('per_occupation', 'like', '%' . $per_occupation . '%');
                }

                // pre_strength_of_character_rate
                if (isset($candidateInfo['pre_strength_of_character_rate']) and !empty($candidateInfo['pre_strength_of_character_rate'])) {
                    $pre_strength_of_character_rate = $candidateInfo['pre_strength_of_character_rate'];
                    $search->orWhere('pre_strength_of_character_rate', 'like', '%' . $pre_strength_of_character_rate . '%');
                }
                // pre_look_and_appearance_rate
                if (isset($candidateInfo['pre_look_and_appearance_rate']) and !empty($candidateInfo['pre_look_and_appearance_rate'])) {
                    $pre_look_and_appearance_rate = $candidateInfo['pre_look_and_appearance_rate'];
                    $search->orWhere('pre_look_and_appearance_rate', 'like', '%' . $pre_look_and_appearance_rate . '%');
                }

                // pre_religiosity_or_faith_rate
                if (isset($candidateInfo['pre_religiosity_or_faith_rate']) and !empty($candidateInfo['pre_religiosity_or_faith_rate'])) {
                    $pre_religiosity_or_faith_rate = $candidateInfo['pre_religiosity_or_faith_rate'];
                    $search->orWhere('pre_religiosity_or_faith_rate', 'like', '%' . $pre_religiosity_or_faith_rate . '%');
                }
                // pre_manners_socialskill_ethics_rate
                if (isset($candidateInfo['pre_manners_socialskill_ethics_rate']) and !empty($candidateInfo['pre_manners_socialskill_ethics_rate'])) {
                    $pre_manners_socialskill_ethics_rate = $candidateInfo['pre_manners_socialskill_ethics_rate'];
                    $search->orWhere('pre_manners_socialskill_ethics_rate', 'like', '%' . $pre_manners_socialskill_ethics_rate . '%');
                }
                // pre_emotional_maturity_rate
                if (isset($candidateInfo['pre_emotional_maturity_rate']) and !empty($candidateInfo['pre_emotional_maturity_rate'])) {
                    $pre_emotional_maturity_rate = $candidateInfo['pre_emotional_maturity_rate'];
                    $search->orWhere('pre_emotional_maturity_rate', 'like', '%' . $pre_emotional_maturity_rate . '%');
                }
                // pre_good_listener_rate
                if (isset($candidateInfo['pre_good_listener_rate']) and !empty($candidateInfo['pre_good_listener_rate'])) {
                    $pre_good_listener_rate = $candidateInfo['pre_good_listener_rate'];
                    $search->orWhere('pre_good_listener_rate', 'like', '%' . $pre_good_listener_rate . '%');
                }

                // pre_good_talker_rate
                if (isset($candidateInfo['pre_good_talker_rate']) and !empty($candidateInfo['pre_good_talker_rate'])) {
                    $pre_good_talker_rate = $candidateInfo['pre_good_talker_rate'];
                    $search->orWhere('pre_good_talker_rate', 'like', '%' . $pre_good_talker_rate . '%');
                }

                // `pre_study_level_id`
                if (isset($candidateInfo['pre_study_level_id']) and !empty($candidateInfo['pre_study_level_id'])) {
                    $per_education_level_id = $candidateInfo['pre_study_level_id'];
                    $search->orWhere('per_education_level_id', 'like', '%' . $per_education_level_id . '%');
                }

                // per_hobbies_interests
                if (isset($per_education_level_id['hobbies_interests']) and !empty($per_education_level_id['hobbies_interests'])) {//
                    $per_hobbies_interests = $per_education_level_id['hobbies_interests'];
                    $search->orWhere('per_hobbies_interests', 'LIKE', '%' . $per_hobbies_interests . '%');
                }
                $gender = $candidateInfo['per_gender'];
                if ($gender == 1) {
                    $gender = 2;
                } elseif ($gender == 2) {
                    $gender = 1;
                } else {
                    $gender = 2;
                }
                $search->where('per_gender', '=', $gender);
                $search->where('user_id', '!=', $userId);
                $page = 1;
                if ($page) {
                    $skip = $parpage * ($page - 1);
                    $queryData = $search->limit($parpage)->offset($skip)->get();
                } else {
                    $queryData = $search->limit($parpage)->offset(0)->get();
                }

                $PaginationCalculation = $search->paginate($parpage);
                $candidate_info = SearchResource::collection($queryData)->filter(function ($value, $key) use ($userId) {
                    return $value->user_id != $userId;
                })->flatten();
                $result['result'] = $candidate_info;
                $result['pagination'] = self::pagination($PaginationCalculation);
                return $this->sendSuccessResponse($result, 'Information fetch Successfully!');
            else:
                $result['result'] = [];
                $result['pagination'] = [];
                return $this->sendSuccessResponse($result, 'user information not found');
            endif;


        elseif ($userInfo['account_type'] == 2):
            $representativeInfo = $this->representativeRepository->findOneBy(['user_id' => $userId]);
            // Religion
//            if (isset($representativeInfo['per_current_residence_country']) and !empty($representativeInfo['per_current_residence_country'])) {
//                $per_current_residence_country = $representativeInfo['per_current_residence_country'];
//                $search->orWhere('per_nationality', 'like', '%' . $per_current_residence_country . '%');
//            }
            $gender = $representativeInfo['per_gender'];
            if ($gender == 1) {
                $gender = 2;
            } elseif ($gender == 2) {
                $gender = 1;
            } else {
                $gender = 2;
            }
            $search->where('per_gender', '=', $gender);
            $page = 1;
            if ($page) {
                $skip = $parpage * ($page - 1);
                $queryData = $search->limit($parpage)->offset($skip)->get();
            } else {
                $queryData = $search->limit($parpage)->offset(0)->get();
            }

            $PaginationCalculation = $search->paginate($parpage);
            $candidate_info = SearchResource::collection($queryData)->where('user_id', '!=', $userId)->where('per_gender', '=', $gender);
            $result['result'] = $candidate_info;
            $result['pagination'] = self::pagination($PaginationCalculation);
            return $this->sendSuccessResponse($result, 'Information fetch Successfully!');

        else:
            $result['result'] = [];
            $result['pagination'] = [];
            return $this->sendSuccessResponse($result, 'Information fetch Successfully!');
        endif;

    }

    /**
     * @param $queryData
     * @return array
     */
    protected function pagination($queryData)
    {
        $data = [
            'total_items' => $queryData->total(),
            'current_items' => $queryData->count(),
            'first_item' => $queryData->firstItem(),
            'last_item' => $queryData->lastItem(),
            'current_page' => $queryData->currentPage(),
            'last_page' => $queryData->lastPage(),
            'has_more_pages' => $queryData->hasMorePages(),
        ];
        return $data;
    }
}
