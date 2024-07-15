<?php

namespace App\Services;

use App\Http\Resources\HomeSearchResource;
use App\Repositories\CandidateRepository;
use App\Repositories\TeamMemberRepository;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use App\Traits\CrudTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeSearchService extends ApiBaseService
{
    use CrudTrait;

    protected \App\Repositories\UserRepository $userRepository;

    protected \App\Services\BlockListService $blockListService;

    protected \App\Repositories\CandidateRepository $candidateRepository;

    protected \App\Repositories\TeamMemberRepository $teamMemberRepository;

    protected \App\Repositories\TeamRepository $teamRepository;

    /**
     * TeamService constructor.
     */
    public function __construct(
        TeamRepository $teamRepository,
        TeamMemberRepository $teamMemberRepository,
        UserRepository $userRepository,
        CandidateRepository $candidateRepository,
        BlockListService $blockListService
    ) {
        $this->teamRepository = $teamRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->userRepository = $userRepository;
        $this->candidateRepository = $candidateRepository;
        $this->blockListService = $blockListService;
        $this->setActionRepository($candidateRepository);
    }

    /**
     * Update resource
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function filter($request)
    {

        $parpage = (isset($request['parpage']) && ! empty($request['parpage'])) ? $request['parpage'] : 10;
        $minAge = (isset($request['min_age']) && ! empty($request['min_age'])) ? Carbon::now()->subYear($request['min_age'])->format('Y-m-d') : Carbon::now()->subYear(16)->format('Y-m-d');
        $maxAge = (isset($request['max_age']) && ! empty($request['max_age'])) ? Carbon::now()->subYear($request['max_age'])->format('Y-m-d') : Carbon::now()->subYear(40)->format('Y-m-d');
        $minHeight = (isset($request['min_height']) && ! empty($request['min_height']) && $request['min_height'] > 3) ? $request['min_height'] : 3;
        $maxHeight = (isset($request['max_height']) && ! empty($request['max_height']) && $request['max_height'] > 3) ? $request['max_height'] : 8;

        try {

            $search = $this->actionRepository->getModel()->newQuery();
            $search->whereBetween('dob', [$maxAge, $minAge]);
            //        $search->whereBetween('per_height', [$minHeight, $maxHeight]);

            // Check user status
            $search->join('users', function ($join) {
                $join->on('users.id', '=', 'candidate_information.user_id')
                    ->where('status', '=', '1');
            });

            // gender
            if (isset($request['gender']) and ! empty($request['gender'])) {
                $gender = $request['gender'];
                $search->where('per_gender', '=', $gender);
            }
            // Religion
            if (isset($request['religion']) and ! empty($request['religion'])) {
                $religion = (int) $request['religion'];
                $search->where('per_religion_id', '=', $religion);
            }
            //  ethnicity
            if (isset($request['ethnicity']) and ! empty($request['ethnicity'])) {
                $per_ethnicity = $request['ethnicity'];
                $search->where('per_ethnicity', '=', $per_ethnicity);
            }
            // per_marital_status
            if (isset($request['marital_status']) and ! empty($request['marital_status'])) {
                $per_marital_status = $request['marital_status'];
                $search->where('per_marital_status', '=', $per_marital_status);
            }
            // per_employment_status
            if (isset($request['employment_status']) and ! empty($request['employment_status'])) {
                $per_employment_status = $request['employment_status'];
                $search->where('per_employment_status', '=', $per_employment_status);
            }

            // per_occupation
            if (isset($request['occupation']) and ! empty($request['occupation'])) {
                $per_occupation = $request['occupation'];
                $search->where('per_occupation', '=', $per_occupation);
            }

            // per_education_level_id
            if (isset($request['education_level_id']) and ! empty($request['education_level_id'])) {
                $per_education_level_id = $request['education_level_id'];
                $search->where('per_education_level_id', '=', $per_education_level_id);
            }

            // country
            if (isset($request['country']) and ! empty($request['country'])) {
                $country = $request['country'];
                $search->where('per_nationality', '=', $country);
            }

            // per_current_residence_country
            if (isset($request['current_residence_country']) and ! empty($request['current_residence_country'])) {
                $current_residence_country = $request['current_residence_country'];
                $search->where('per_current_residence_country', '=', $current_residence_country);
            }
            // per_currently_living_with
            if (isset($request['per_currently_living_with']) and ! empty($request['per_currently_living_with'])) {
                $currently_living_with = $request['per_currently_living_with'];
                $search->where('per_currently_living_with', '=', $currently_living_with);
            }
            // per_smoker
            if (isset($request['smoker']) and ! empty($request['smoker'])) {
                $per_smoker = $request['smoker'];
                $search->where('per_smoker', '=', $per_smoker);
            }

            $page = $request['page'] ?: 1;
            if ($page < 1) {
                $page = 1;
            }
            if ($page) {
                $skip = $parpage * ($page - 1);
                $queryData = $search->limit($parpage)->offset($skip)->get();
            } else {
                $queryData = $search->limit($parpage)->offset(0)->get();
            }
            $PaginationCalculation = $search->paginate($parpage);
            $candidate_info = HomeSearchResource::collection($queryData);
            $result['result'] = $candidate_info;
            $result['pagination'] = self::pagination($PaginationCalculation);

            return $this->sendSuccessResponse($result, 'Information fetch Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

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
