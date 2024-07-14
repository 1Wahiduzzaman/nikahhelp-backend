<?php

namespace App\Services;

//use App\Enums\HttpStatusCode;
//use App\Models\Team;
//use Exception;
//use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\TeamConnectionRepository;
use App\Repositories\TeamMemberRepository;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\JsonResponse;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use App\Transformers\TeamTransformer;
//use Illuminate\Support\Str;
//use App\Services\AccessRulesDefinitionService;
//use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

//use App\Models\TeamMember;
//use App\Models\TeamConnection;
//use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Database\QueryException;

class ChatInfoService extends ApiBaseService
{
    use CrudTrait;

    protected \App\Repositories\UserRepository $userRepository;

    protected \App\Repositories\TeamMemberRepository $teamMemberRepository;

    protected \App\Repositories\TeamRepository $teamRepository;

    protected \App\Repositories\TeamConnectionRepository $teamConnectionRepository;

    /**
     * TeamService constructor.
     */
    public function __construct(TeamRepository $teamRepository,
        TeamMemberRepository $teamMemberRepository,
        UserRepository $userRepository,
        TeamConnectionRepository $teamConnectionRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->userRepository = $userRepository;
        $this->teamConnectionRepository = $teamConnectionRepository;
    }

    /**
     * Update resource
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getInfo($request)
    {
        //"success from service";
        $conwise_memberlist = $this->getConWiseMemberList();
        $data = [];
        $data['conwise_info'] = $conwise_memberlist;

        return $this->sendSuccessResponse($data, 'Data fetched successfully!');
    }

    /**
     * Update resource
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getUserInfoList($request)
    {
        //"success from service";
        $user_id = Auth::id();

        $all_team_ids = [];
        $my_team_ids = [];
        $user_team_list = $this->teamMemberRepository->findByProperties([
            'user_id' => $user_id,
        ]);

        foreach ($user_team_list as $row) {
            array_push($all_team_ids, $row->team_id);
            array_push($my_team_ids, $row->team_id);
        }

        foreach ($my_team_ids as $team_id) {
            $connected_teams1 = $this->teamConnectionRepository->findByProperties([
                'from_team_id' => $team_id,
            ]);
            foreach ($connected_teams1 as $row) {
                array_push($all_team_ids, $row->to_team_id);
            }
            $connected_teams2 = $this->teamConnectionRepository->findByProperties([
                'to_team_id' => $team_id,
            ]);
            foreach ($connected_teams2 as $row) {
                array_push($all_team_ids, $row->from_team_id);
            }
        }

        $all_team_members = DB::table('team_members')
            ->whereIn('team_id', $all_team_ids)
            ->get();
        $all_users = [];
        foreach ($all_team_members as $row) {
            array_push($all_users, $row->user_id);
        }

        $candidates_id = [];
        $result_data = [];

        $candidates = DB::table('candidate_information')
            ->whereIn('user_id', $all_users)
            ->select('user_id', 'first_name', 'last_name', 'per_main_image_url')
            ->get();

        foreach ($candidates as $row) {
            $data = $row;
            $data->per_main_image_url = url('storage/'.$row->per_main_image_url);
            array_push($result_data, $data);
            array_push($candidates_id, $data->user_id);
        }

        $reps = DB::table('representative_informations')
            ->whereIn('user_id', $all_users)
            ->select('user_id', 'first_name', 'last_name', 'per_main_image_url')
            ->get();

        foreach ($reps as $row) {
            if (! in_array($row->user_id, $candidates_id)) {
                $data = $row;
                $data->per_main_image_url = url('storage/'.$row->per_main_image_url);
                array_push($result_data, $data);
            }
        }

        return $this->sendSuccessResponse($result_data, 'Data fetched successfully!');
    }

    public function getConWiseMemberList()
    {
        $con_wise_memberlist = [];
        $con_wise_title = [];
        $from_team_members = DB::table('team_connections AS TC')
            ->join('team_members AS FT', 'TC.from_team_id', '=', 'FT.team_id')
            ->join('teams AS F', 'TC.from_team_id', '=', 'F.id')
            ->select('TC.id',
                'F.name',
                DB::raw('GROUP_CONCAT(FT.user_id) as from_team_members')
            )
            ->groupByRaw('TC.id')
            ->get();

        foreach ($from_team_members as $row) {
            $member_array = explode(',', $row->from_team_members);
            $con_wise_memberlist[$row->id] = $member_array;
            $con_wise_title[$row->id] = $row->name;
        }

        $to_team_members = DB::table('team_connections AS TC')
            ->join('team_members AS TT', 'TC.to_team_id', '=', 'TT.team_id')
            ->join('teams AS T', 'TC.to_team_id', '=', 'T.id')
            ->select('TC.id',
                'T.name',
                DB::raw('GROUP_CONCAT(TT.user_id) as to_team_members')
            )
            ->groupByRaw('TC.id')
            ->get();

        foreach ($to_team_members as $row) {
            $new_member_array = explode(',', $row->to_team_members);
            if (isset($con_wise_memberlist[$row->id])) {
                $previous_value = $con_wise_memberlist[$row->id];
            } else {
                $previous_value = [];
            }

            if (isset($con_wise_title[$row->id])) {
                $previous_title = $con_wise_title[$row->id];
            } else {
                $previous_title = '';
            }

            $new_title = "$previous_title & ".$row->name;
            $total = array_merge($new_member_array, $previous_value);
            $con_wise_memberlist[$row->id] = $total;
            $con_wise_title[$row->id] = $new_title;
        }
        $result = [];
        foreach ($con_wise_memberlist as $key => $value) {
            $row = [];
            $members = [];
            foreach ($value as $temp_user_id) {
                array_push($members, (int) $temp_user_id);
            }
            $row['con_id'] = $key;
            $row['title'] = $con_wise_title[$key];
            $row['member_list'] = $members;
            array_push($result, $row);
        }

        return $result;
    }
}
