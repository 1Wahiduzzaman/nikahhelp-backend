<?php


namespace App\Services;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Repositories\BlockListRepository;
use App\Http\Resources\BlockListResource;
use Symfony\Component\HttpFoundation\Response as FResponse;
use Carbon\Carbon;
use App\Repositories\ShortListedCandidateRepository;
use App\Helpers\Notificationhelpers;
use App\Models\BlockList;
use App\Models\TeamMember;

class BlockListService extends ApiBaseService
{
    use CrudTrait;

    const INFORMATION_FETCHED_SUCCESSFULLY = 'Information fetched Successfully!';
    const INFORMATION_UPDATED_SUCCESSFULLY = 'Information updated Successfully!';
    const INFORMATION_SAVE_SUCCESSFULLY = 'Information save Successfully!';

    /**
     * @var BlockListRepository
     */
    protected $blockListRepository;

    /**
     * @var BlockListResource
     */
    protected $blockListResource;

    /**
     * @var  ShortListedCandidateRepository
     */
    private $shortListedCandidateRepository;

    /**
     * CandidateService constructor.
     *
     * @param BlockListRepository $blockListRepository
     */
    public function __construct(BlockListRepository $blockListRepository, ShortListedCandidateRepository $shortListedCandidateRepo)
    {
        $this->blockListRepository = $blockListRepository;
        $this->shortListedCandidateRepository = $shortListedCandidateRepo;
    }


    /**
     * Update resource
     * @param Request $request
     * @return JsonResponse
     */
    public function store($request)
    {        
        $userId = self::getUserId();        
        try {
            $candidate = $this->blockListRepository->findOneBy([              
                'user_id' => $request['user_id'],
                'block_by' => $request['block_by']
            ]);
            if ($candidate) {
                return $this->sendErrorResponse('Information Already Exists', [], FResponse::HTTP_CONFLICT);
            }
            self::checkShortListCanddate($candidate, $request);
            $input = $request;
            $input['block_date'] = Carbon::now()->format('Y-m-d');
            $blocklist = $this->blockListRepository->save($input);
            Notificationhelpers::add('Successfully blocked ! you can find this user in your block list', 'single', null, $userId);
            return $this->sendSuccessResponse($blocklist->toArray(), self::INFORMATION_SAVE_SUCCESSFULLY);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $request
     * @return bool|JsonResponse
     */
    public function checkShortListCanddate($candidate, $request)
    {

        if (!$candidate && $request['type'] == 'single') {
            $checkShortlisted = $this->shortListedCandidateRepository->findOneBy([
                'user_id' => $request['user_id'],
                'shortlisted_by' => $request['block_by'],
                'shortlisted_for' => null,
            ]);
            if ($checkShortlisted) {
                $checkShortlisted->delete();
            }
        }

        if (!$candidate && $request['type'] == 'team') {
            $checkShortlisted = $this->shortListedCandidateRepository->findOneBy([
                'user_id' => $request['user_id'],
                'shortlisted_by' => $request['block_by'],
                'shortlisted_for' => !null,
            ]);
            if ($checkShortlisted) {
                $checkShortlisted->delete();
            }

        }
        return true;
    }

    /**
     * @param $userId
     * @return mixed
     * This function user for search candidate block for him
     *  Function return user list or User id
     */
    public function blockListByUser($userId)
    {
        return $list = BlockList::where('block_by', '=', $userId)->pluck('user_id');
    }

    /**
     * @param $userId
     * @return |null
     *  This function use in sear api for getting user team and block candidate by team
     */
    public function getTeamBlockListByUser($userId)
    {
        $joinTeamList = TeamMember::where('user_id', '=', $userId)->groupBy('team_id')->pluck('team_id');

        if (count($joinTeamList) >= 1):
            return $list = BlockList::whereIn('block_for', $joinTeamList)->pluck('user_id');
        else:
            return null;
        endif;
    }

    /**
     * @param $userId
     * @return |null
     *  This function use in sear api for getting user team and block candidate by team
     */
    public function getActiveTeamBlockListByUser($activeTeamId)
    {
        $joinTeamList = BlockList::where('block_for', $activeTeamId)->pluck('user_id');

        if (count($joinTeamList) >= 1):
            return $joinTeamList;
        else:
            return null;
        endif;
    }

}
