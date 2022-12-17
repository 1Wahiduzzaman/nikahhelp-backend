<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Models\Generic;
use App\Models\Team;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\TeamRepository;
use App\Repositories\TeamMemberRepository;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;
use App\Transformers\TeamTransformer;
use Illuminate\Support\Str;
use App\Services\AccessRulesDefinitionService;
use Illuminate\Support\Carbon;
use App\Repositories\TeamConnectionRepository;
use App\Models\TeamMember;
use App\Models\TeamConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use App\Http\Resources\TeamConnectionResource;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;

class TeamConnectionService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * @var TeamConnectionRepository
     */
    protected $teamConnectionRepository;


    /**
     * TeamService constructor.
     *
     * @param TeamRepository $teamRepository
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
     * @param Request $request
     * @return JsonResponse
     */
    public function sendRequest($request)
    {
        $from_team = $this->teamRepository->findOneByProperties([
            "team_id" => $request->from_team_id
        ]);


        if (!$from_team) {
            return $this->sendErrorResponse('From team not found.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        $to_team = $this->teamRepository->findOneByProperties([
            "team_id" => $request->to_team_id
        ]);

        // $expire_date = $to_team->subscription_expire_at;

        // $expired_now = $expire_date->lessThanOrEqualTo(now());


        // if ($expired_now) {
        //     return $this->sendErrorResponse('Team expired', [], HttpStatusCode::NOT_FOUND);
        // }

        // Log::info('sending');

        if (!$to_team) {
            return $this->sendErrorResponse('To team not found.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        /// verification validation start
        // from team candidate must be verified
        $from_team_id = $from_team->id;
        $from_team_candidate = TeamMember::select("*")
            ->with("user")
            ->where('team_id', $from_team_id)
            ->where('user_type', "Candidate")
            ->get();
//        dd($from_team_id,$from_team_candidate);
        if (count($from_team_candidate) > 0) {
            $from_team_candidate_user = $from_team_candidate[0]->user;
            if ($from_team_candidate_user->is_verified != 1) {
                return $this->sendErrorResponse('Your Team candidate is not verified.', [], HttpStatusCode::VALIDATION_ERROR);
            }
        } else {
            return $this->sendErrorResponse('No candidate found in your team.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // to team candidate must be verified
        $to_team_id = $to_team->id;
        $to_team_candidate = TeamMember::select("*")
            ->with("user")
            ->where('team_id', "$to_team_id")
            ->where('user_type', "Candidate")
            ->get();

        if (count($to_team_candidate) > 0) {
            $to_team_candidate_user = $to_team_candidate[0]->user;
            if ($to_team_candidate_user->is_verified != 1) {
                return $this->sendErrorResponse('Their Team candidate is not verified.', [], HttpStatusCode::VALIDATION_ERROR);
            }
        } else {
            return $this->sendErrorResponse('No candidate found in their team.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // at least 1 representative must be verified from "from_team"
        $from_team_verified_reps = TeamMember::select("*")
            ->with("user")
            ->where('team_id', $from_team_id)
            ->where('user_type', "Representative")
            ->get();

        if (count($from_team_verified_reps) > 0) {
            $from_team_verified_reps_user = $from_team_verified_reps[0]->user;
            if ($from_team_verified_reps_user->is_verified != 1) {
                return $this->sendErrorResponse('Your team representative is not verified.', [], HttpStatusCode::VALIDATION_ERROR);
            }
        } else {
            return $this->sendErrorResponse('No verified representative found in your team.', [], HttpStatusCode::VALIDATION_ERROR);

        }


        // at least 1 representative must be verified from "to_team"
        $to_team_verified_reps = TeamMember::select("*")
            ->with("user")
            ->where('team_id', $to_team_id)
            ->where('user_type', "Representative")
            ->get();
        if (count($to_team_verified_reps) > 0) {
            $to_team_verified_reps_user = $from_team_verified_reps[0]->user;
            if ($to_team_verified_reps_user->is_verified != 1) {
                return $this->sendErrorResponse('Their Team representative is not verified.', [], HttpStatusCode::VALIDATION_ERROR);
            }
        } else {
            return $this->sendErrorResponse('No verified representative found in their team.', [], HttpStatusCode::VALIDATION_ERROR);

        }
        /// verification validation end

        // Check previous connection request
        $previous_reqs = TeamConnection::select("*")
            ->where(function ($query) use ($from_team_id, $to_team_id) {
                $query->where('from_team_id', '=', $from_team_id)
                    ->where('to_team_id', '=', $to_team_id);
            })
            ->orWhere(function ($query) use ($from_team_id, $to_team_id) {
                $query->where('from_team_id', '=', $to_team_id)
                    ->where('to_team_id', '=', $from_team_id);
            })
            ->get();

        if (count($previous_reqs) == 0) {
            $data = array();
            $data["requested_by"] = self::getUserId();
            $data["from_team_id"] = $from_team->id;
            $data["to_team_id"] = $to_team->id;

            try {
                $team_connection = $this->teamConnectionRepository->save($data);
                return $this->sendSuccessResponse($team_connection, 'Request sent successfully!');
            } catch (Exception $ex) {
                return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::VALIDATION_ERROR);
            }
        } else {
            // Check request status
            $team_connection = $previous_reqs[0];
            if ($team_connection->connection_status == "2") {
                // If it was rejected by any side update connection to pending again
                $connection_row = $this->teamConnectionRepository->findOneByProperties([
                    "id" => $team_connection->id
                ]);
                $connection_row->from_team_id = $from_team->id;
                $connection_row->to_team_id = $to_team->id;
                $connection_row->requested_by = self::getUserId();
                $connection_row->requested_at = Carbon::now()->toDateTimeString();
                $connection_row->connection_status = '0';
                $connection_row->responded_by = NULL;
                $connection_row->responded_at = NULL;

                $input = (array)$connection_row;
                // As BaseRepository update method has bug that's why we have to fallback to model default methods.
                $input = $connection_row->fill($input)->toArray();
                $connection_row->save($input);
                return $this->sendSuccessResponse($connection_row, 'Connection request resent!');
            }

            return $this->sendSuccessResponse($previous_reqs[0], 'Your previous request is pending at their end. We have notified them!');
        }
    }

    /**
     * Update resource
     * @param Request $request
     * @return JsonResponse
     */
    public function respondRequest($request)
    {
        $user_id = self::getUserId();
        $connection_request = $this->teamConnectionRepository->findOneByProperties([
            "id" => $request->request_id
        ]);

        if (!$connection_request) {
            return $this->sendErrorResponse('Connection request not found.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        $to_team_id = $connection_request->to_team_id;

        $update_response = $this->updateResponse($user_id, $to_team_id, $connection_request, $request->connection_status);
        return $update_response;
    }


    public function updateResponse($user_id, $team_id, $connection_row, $connection_status)
    {
        if($connection_status ==10) {
            $connection_row = TeamConnection::where('id', $connection_row->id)->delete();
            return $this->sendSuccessResponse($connection_row, 'Response updated successfully!');
        } else {
            // If connection status is pending only "To Team" can update the connection status
        $user_member_status = $this->teamMemberRepository->findOneByProperties(
            [
                "user_id" => $user_id,
                "team_id" => $team_id
            ]
        );

        if (!$user_member_status) {
            return $this->sendErrorResponse("You are no longer a member of the connection requested team.", [], HttpStatusCode::NOT_FOUND);
        }

        $access_rules = new AccessRulesDefinitionService();
        $respond_connection_rights = $access_rules->hasRespondConnectionRequestRights();
        if (!in_array($user_member_status->role, $respond_connection_rights)) {
            return $this->sendErrorResponse("You dont have rights to accept/reject connection request.", [], HttpStatusCode::VALIDATION_ERROR);
        }

        if (!is_numeric($connection_status)) {
            return $this->sendErrorResponse("Invalid connection status.Valid Values[0=>pending,1=>accepted,2=>rejected]", [], HttpStatusCode::VALIDATION_ERROR);
        }

        if (!in_array($connection_status, ["0", "1", "2"])) {
            return $this->sendErrorResponse("Invalid connection status.Valid Values[0=>pending,1=>accepted,2=>rejected]", [], HttpStatusCode::VALIDATION_ERROR);
        }

        $connection_row->connection_status = $connection_status;
        $connection_row->responded_by = $user_id;
        $connection_row->responded_at = Carbon::now()->toDateTimeString();
        $input = (array)$connection_row;
        $input = $connection_row->fill($input)->toArray();

        try {
            $connection_row->save($input);
            return $this->sendSuccessResponse($connection_row, 'Response updated successfully!');
        } catch (QueryException $ex) {
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::VALIDATION_ERROR);
        }
        }
    }

    public function reports($request)
    {
        $connected_teams = 0;
        $request_received = 0;
        $request_sent = 0;
        $we_declined = 0;
        $they_declined = 0;
        $teamId = $request->team_id;
        $userId = self::getUserId();
        $teamInformation = Team::where("team_id", '=', "$teamId")
        ->where('status',1)
        ->first();
        if (empty($teamInformation)) {
            return $this->sendErrorResponse('Active team information not found', [], HttpStatusCode::NOT_FOUND);
        }
        // Connected
        $connection_status = 1;
        $searchResult = $this->teamConnectionRepository->getModel()->newQuery();

        $searchResult->where('from_team_id', $teamInformation->id);
        $searchResult->orWhere('to_team_id', $teamInformation->id);
        $queryData = $searchResult->get();
        if (!empty($queryData) && count($queryData)) {
            foreach ($queryData as $key => $rInput) {
                $queryData[$key]['active_teams'] = $teamInformation->id;
            }
        }

        foreach ($queryData as $info) {
            if ($info->connection_status == 0 && $info->from_team_id == $teamInformation->id) {
                $request_sent++;
            }
            if ($info->connection_status == 0 && $info->to_team_id == $teamInformation->id) {
                $request_received++;
            }
            if ($info->connection_status == 1 && ($info->to_team_id == $teamInformation->id or $info->from_team_id == $teamInformation->id)) {
                $connected_teams++;
            }
            if ($info->connection_status == 2 && $info->to_team_id == $teamInformation->id) {
                $we_declined++;
            }
            if ($info->connection_status == 2 && $info->from_team_id == $teamInformation->id) {
                $they_declined++;
            }

        }

        $resultInfo = TeamConnectionResource::collection($queryData);
        //dd($resultInfo);
        $data = array();
        $data['result'] = $resultInfo;
        $data["connected_teams"] = $connected_teams;
        $data["request_received"] = $request_received;
        $data["request_sent"] = $request_sent;
        $data["we_declined"] = $we_declined;
        $data["they_declined"] = $they_declined;


        return $this->sendSuccessResponse($data, 'Data fetched successfully!');


    }

    /**
     * Update resource
     * @param Request $request
     * @return JsonResponse
     */
    public function report($request)
    {
        $team_id = $request->team_id;
        $team = $this->teamRepository->findOneByProperties([
            "team_id" => $team_id
        ]);

        if (!$team) {
            return $this->sendErrorResponse("Team not found.", [], HttpStatusCode::NOT_FOUND);
        }

        $team_row_id = $team->id;

        // SQL query explanation: We need to join teams table twice with alias to Get From Team and To Team information
        // Team member information is needed to find out the candidates in both teams.
        // Candidate information table is joined to get the candidate data
        // If user row id is match with to_team then from_team candidate data will be selected and vice versa as user wants to
        // see the information of the opposite team.

        // Connected
        $connection_status = 1;
        try {
            $connected_teams_1 = DB::table('team_connections AS TCon')
                ->join('teams AS ToTeam', 'TCon.to_team_id', '=', 'ToTeam.id')
                ->join('team_members AS ToTeamCandidateMember', function ($join) {
                    $join->on('ToTeam.id', '=', 'ToTeamCandidateMember.team_id');
                    $join->where('ToTeamCandidateMember.user_type', '=', 'Candidate');
                })
                ->join('candidate_information AS ToCandidate', 'ToTeamCandidateMember.user_id', '=', 'ToCandidate.user_id')
                ->where(function ($query) use ($connection_status, $team_row_id) {
                    $query->where('TCon.connection_status', '=', "$connection_status")
                        ->where('TCon.from_team_id', '=', "$team_row_id");
                })
                ->leftJoin('countries', 'ToCandidate.per_current_residence_country', '=', 'countries.id')
                ->leftJoin('religions', 'ToCandidate.per_religion_id', '=', 'religions.id')
                ->select(
                    'TCon.id as connection_id',
                    'ToTeam.team_id as team_id',
                    'ToTeam.id as team_table_id',
                    'ToTeam.name as team_name',
                    'ToCandidate.first_name as candidate_fname',
                    'ToCandidate.last_name as candidate_lname',
                    DB::raw('TIMESTAMPDIFF(YEAR,ToCandidate.dob,now()) as candidate_age'),
                    'ToCandidate.per_current_residence_country as candidate_location',
                    'ToCandidate.per_ethnicity as candidate_ethnicity',
                    'ToCandidate.per_main_image_url as candidate_image',
                    'countries.name as candidate_location',
                    'religions.name as candidate_religion',
                    'ToCandidate.user_id as candidate_userid')
                ->get();
            // Closures include ->first(), ->get(), ->pluck(), etc.
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND);
            // Note any method of class PDOException can be called on $ex.
        }


        try {
            $connected_teams_2 = DB::table('team_connections AS TCon')
                ->join('teams AS FromTeam', 'TCon.from_team_id', '=', 'FromTeam.id')
                ->join('team_members AS FromCandidateMember', function ($join) {
                    $join->on('FromTeam.id', '=', 'FromCandidateMember.team_id');
                    $join->where('FromCandidateMember.user_type', '=', 'Candidate');
                })
                ->join('candidate_information AS FromCandidate', 'FromCandidateMember.user_id', '=', 'FromCandidate.user_id')
                ->where(function ($query) use ($connection_status, $team_row_id) {
                    $query->where('TCon.connection_status', '=', "$connection_status")
                        ->where('TCon.to_team_id', '=', "$team_row_id");
                })
                ->leftJoin('countries', 'FromCandidate.per_current_residence_country', '=', 'countries.id')
                ->leftJoin('religions', 'FromCandidate.per_religion_id', '=', 'religions.id')
                ->select(
                    'TCon.id as connection_id',
                    'FromTeam.team_id as team_id',
                    'FromTeam.id as team_table_id',
                    'FromTeam.name as team_name',
                    'FromCandidate.first_name as candidate_fname',
                    'FromCandidate.last_name as candidate_lname',
                    DB::raw('TIMESTAMPDIFF(YEAR,FromCandidate.dob,now()) as candidate_age'),
                    'FromCandidate.per_current_residence_country as candidate_location',
                    'FromCandidate.per_ethnicity as candidate_ethnicity',
                    'FromCandidate.per_main_image_url as candidate_image',
                    'countries.name as candidate_location',
                    'religions.name as candidate_religion',
                    'FromCandidate.user_id as candidate_userid')
                ->get();
            // Closures include ->first(), ->get(), ->pluck(), etc.
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND);
        }


//        $connected_teams = array_merge($connected_teams_1,$connected_teams_2);
        $connected_teams = $connected_teams_1->concat($connected_teams_2);
        $connected_teams = $this->formatImageUrls($connected_teams);

        // Request received
        $connection_status = 0;
        try {
            $request_received = DB::table('team_connections AS TCon')
                ->join('teams AS FromTeam', 'TCon.from_team_id', '=', 'FromTeam.id')
                ->join('team_members AS FromCandidateMember', function ($join) {
                    $join->on('FromTeam.id', '=', 'FromCandidateMember.team_id');
                    $join->where('FromCandidateMember.user_type', '=', 'Candidate');
                })
                ->join('candidate_information AS FromCandidate', 'FromCandidateMember.user_id', '=', 'FromCandidate.user_id')
                ->where(function ($query) use ($connection_status, $team_row_id) {
                    $query->where('TCon.connection_status', '=', "$connection_status")
                        ->where('TCon.to_team_id', '=', "$team_row_id");
                })
                ->leftJoin('countries', 'FromCandidate.per_current_residence_country', '=', 'countries.id')
                ->leftJoin('religions', 'FromCandidate.per_religion_id', '=', 'religions.id')
                ->select(
                    'TCon.id as connection_id',
                    'FromTeam.team_id as team_id',
                    'FromTeam.id as team_table_id',
                    'FromTeam.name as team_name',
                    'FromCandidate.first_name as candidate_fname',
                    'FromCandidate.last_name as candidate_lname',
                    DB::raw('TIMESTAMPDIFF(YEAR,FromCandidate.dob,now()) as candidate_age'),
                    'FromCandidate.per_current_residence_country as candidate_location',
                    'FromCandidate.per_ethnicity as candidate_ethnicity',
                    'FromCandidate.per_main_image_url as candidate_image',
                    'countries.name as candidate_location',
                    'religions.name as candidate_religion',
                    'FromCandidate.user_id as candidate_userid')
                ->get();
            // Closures include ->first(), ->get(), ->pluck(), etc.
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND);
        }

        $request_received = $this->formatImageUrls($request_received);

        // Request sent
        $connection_status = 0;

        try {
            $request_sent = DB::table('team_connections AS TCon')
                ->join('teams AS ToTeam', 'TCon.to_team_id', '=', 'ToTeam.id')
                ->join('team_members AS ToTeamCandidateMember', function ($join) {
                    $join->on('ToTeam.id', '=', 'ToTeamCandidateMember.team_id');
                    $join->where('ToTeamCandidateMember.user_type', '=', 'Candidate');
                })
                ->join('candidate_information AS ToCandidate', 'ToTeamCandidateMember.user_id', '=', 'ToCandidate.user_id')
                ->where(function ($query) use ($connection_status, $team_row_id) {
                    $query->where('TCon.connection_status', '=', "$connection_status")
                        ->where('TCon.from_team_id', '=', "$team_row_id");
                })
                ->leftJoin('countries', 'ToCandidate.per_current_residence_country', '=', 'countries.id')
                ->leftJoin('religions', 'ToCandidate.per_religion_id', '=', 'religions.id')
                ->select(
                    'TCon.id as connection_id',
                    'ToTeam.team_id as team_id',
                    'ToTeam.id as team_table_id',
                    'ToTeam.name as team_name',
                    'ToCandidate.first_name as candidate_fname',
                    'ToCandidate.last_name as candidate_lname',
                    DB::raw('TIMESTAMPDIFF(YEAR,ToCandidate.dob,now()) as candidate_age'),
                    'ToCandidate.per_current_residence_country as candidate_location',
                    'ToCandidate.per_ethnicity as candidate_ethnicity',
                    'ToCandidate.per_main_image_url as candidate_image',
                    'countries.name as candidate_location',
                    'religions.name as candidate_religion',
                    'ToCandidate.user_id as candidate_userid')
                ->get();
            // Closures include ->first(), ->get(), ->pluck(), etc.
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND);
        }


        $request_sent = $this->formatImageUrls($request_sent);

        // We declined
        $connection_status = 2;

        try {
            $we_declined = DB::table('team_connections AS TCon')
                ->join('teams AS FromTeam', 'TCon.from_team_id', '=', 'FromTeam.id')
                ->join('team_members AS FromCandidateMember', function ($join) {
                    $join->on('FromTeam.id', '=', 'FromCandidateMember.team_id');
                    $join->where('FromCandidateMember.user_type', '=', 'Candidate');
                })
                ->join('candidate_information AS FromCandidate', 'FromCandidateMember.user_id', '=', 'FromCandidate.user_id')
                ->where(function ($query) use ($connection_status, $team_row_id) {
                    $query->where('TCon.connection_status', '=', "$connection_status")
                        ->where('TCon.to_team_id', '=', "$team_row_id");
                })
                ->leftJoin('countries', 'FromCandidate.per_current_residence_country', '=', 'countries.id')
                ->leftJoin('religions', 'FromCandidate.per_religion_id', '=', 'religions.id')
                ->select(
                    'TCon.id as connection_id',
                    'FromTeam.team_id as team_id',
                    'FromTeam.id as team_table_id',
                    'FromTeam.name as team_name',
                    'FromCandidate.first_name as candidate_fname',
                    'FromCandidate.last_name as candidate_lname',
                    DB::raw('TIMESTAMPDIFF(YEAR,FromCandidate.dob,now()) as candidate_age'),
                    'FromCandidate.per_current_residence_country as candidate_location',
                    'FromCandidate.per_ethnicity as candidate_ethnicity',
                    'FromCandidate.per_main_image_url as candidate_image',
                    'countries.name as candidate_location',
                    'religions.name as candidate_religion',
                    'FromCandidate.user_id as candidate_userid')
                ->get();
            // Closures include ->first(), ->get(), ->pluck(), etc.
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND);
        }

        $we_declined = $this->formatImageUrls($we_declined);

        // They declined
        $connection_status = 2;
        try {
            $they_declined = DB::table('team_connections AS TCon')
                ->join('teams AS ToTeam', 'TCon.to_team_id', '=', 'ToTeam.id')
                ->join('team_members AS ToTeamCandidateMember', function ($join) {
                    $join->on('ToTeam.id', '=', 'ToTeamCandidateMember.team_id');
                    $join->where('ToTeamCandidateMember.user_type', '=', 'Candidate');
                })
                ->join('candidate_information AS ToCandidate', 'ToTeamCandidateMember.user_id', '=', 'ToCandidate.user_id')
                ->where(function ($query) use ($connection_status, $team_row_id) {
                    $query->where('TCon.connection_status', '=', "$connection_status")
                        ->where('TCon.from_team_id', '=', "$team_row_id");
                })
                ->leftJoin('countries', 'ToCandidate.per_current_residence_country', '=', 'countries.id')
                ->leftJoin('religions', 'ToCandidate.per_religion_id', '=', 'religions.id')
                ->select(
                    'TCon.id as connection_id',
                    'ToTeam.team_id as team_id',
                    'ToTeam.id as team_table_id',
                    'ToTeam.name as team_name',
                    'ToCandidate.first_name as candidate_fname',
                    'ToCandidate.last_name as candidate_lname',
                    DB::raw('TIMESTAMPDIFF(YEAR,ToCandidate.dob,now()) as candidate_age'),
                    'ToCandidate.per_ethnicity as candidate_ethnicity',
                    'ToCandidate.per_main_image_url as candidate_image',
                    'countries.name as candidate_location',
                    'religions.name as candidate_religion',
                    'ToCandidate.user_id as candidate_userid')
                ->get();
            // Closures include ->first(), ->get(), ->pluck(), etc.
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND);
        }

        $they_declined = $this->formatImageUrls($they_declined);

        $data = array();
        $data["connected_teams"] = $connected_teams;
        $data["request_received"] = $request_received;
        $data["request_sent"] = $request_sent;
        $data["we_declined"] = $we_declined;
        $data["they_declined"] = $they_declined;

        return $this->sendSuccessResponse($data, 'Data fetched successfully!');
    }

    public function formatImageUrls($dataArray)
    {
        for ($i = 0; $i < count($dataArray); $i++) {
            if (!empty($dataArray[$i]->candidate_image)) {
                $dataArray[$i]->candidate_image = url('storage/' . $dataArray[$i]->candidate_image);
            }
        }
        return $dataArray;
    }

    /**
     * Update resource
     * @param Request $request
     * @return JsonResponse
     */
    public function overview($request)
    {
        $connection_id = $request->connection_id;
        $team_id = $request->team_id;

        $team_connection = TeamConnection::select("*")
            ->with("requested_by_user")
            ->where('id', $connection_id)
            ->get();

        if (count($team_connection) > 0) {
            $connection_details = $team_connection[0];
            $requested_by = $connection_details->requested_by_user;
            $responded_by = $connection_details->responded_by_user;
        } else {
            // send connection not found exception
            return $this->sendErrorResponse("Connection not found.", [], HttpStatusCode::NOT_FOUND);
        }


        $connection_overview = array();
        if ($connection_details->connection_status == "0") {
            $connection_overview["connection_status"] = "Pending";
        } else if ($connection_details->connection_status == "1") {
            $connection_overview["connection_status"] = "Connected";
        } else if ($connection_details->connection_status == "2") {
            $connection_overview["connection_status"] = "Rejected";
        } else {
            $connection_overview["connection_status"] = "Invalid";
        }
        $connection_overview["requested_by"] = $requested_by;
        $connection_overview["requested_at"] = $connection_details->requested_at;
        $connection_overview["responded_by"] = $responded_by;
        $connection_overview["responded_at"] = $connection_details->responded_at;

        $profile_team_overview = array();
        $teams = Team::select("*")
            ->with("user")
            ->where('team_id', $team_id)
            ->get();
        if (count($teams) > 0) {
            $user_team = $teams[0];
            $created_by = $user_team->user;
            $profile_team_overview["team_id"] = $team_id;
            $profile_team_overview["team_name"] = $user_team->name;
            $profile_team_overview["member_count"] = $user_team->member_count;
            $profile_team_overview["team_creation_date"] = $user_team->created_at;
            $profile_team_overview["team_created_by"] = $created_by;
        } else {
            // send team not found exception
            return $this->sendErrorResponse("Team not found.", [], HttpStatusCode::NOT_FOUND);
        }

        $data = array();
        $data["connection_overview"] = $connection_overview;
        $data["profile_team_overview"] = $profile_team_overview;

        return $this->sendSuccessResponse($data, 'Data fetched successfully!');
    }

    /**
     *
     * Update resource
     * @param Request $request
     * @return JsonResponse
     */
    public function disconnect($request)
    {
        $user_id = self::getUserId();
        $connection_id = $request->connection_id;

        $connection_row = $this->teamConnectionRepository->findOneByProperties([
            'id' => $connection_id
        ]);

        if (!$connection_row) {
            // send connection not found exception
            return $this->sendErrorResponse("Connection data not found.", [], HttpStatusCode::NOT_FOUND);
        }

        // Find user member status
        $team_member_info = $this->teamMemberRepository->findOneByProperties(
            [
                "user_id" => $user_id,
                "team_id" => $connection_row->from_team_id
            ]
        );

        if (!$team_member_info) {
            $team_member_info = $this->teamMemberRepository->findOneByProperties(
                [
                    "user_id" => $user_id,
                    "team_id" => $connection_row->to_team_id
                ]
            );

            if (!$team_member_info) {
                // Send team member info not found exception
                return $this->sendErrorResponse("Your member info not found.", [], HttpStatusCode::NOT_FOUND);
            }
        }

        // check user capability
        $user_role = $team_member_info->role;
        $access_rules = new AccessRulesDefinitionService();
        $disconnection_rights = $access_rules->hasDisconnectionRights();
        if (!in_array($user_role, $disconnection_rights)) {
            return $this->sendErrorResponse("You dont have rights to disconnect team.", [], HttpStatusCode::VALIDATION_ERROR);
        }

        // Proceed to disconnect
        $connection_row->connection_status = $request->connection_status;
        $connection_row->responded_by = self::getUserId();
        $connection_row->responded_at = Carbon::now()->toDateTimeString();

        $input = (array)$connection_row;
        // As BaseRepository update method has bug that's why we have to fallback to model default methods.
        $input = $connection_row->fill($input)->toArray();
        $connection_row->save($input);

        return $this->sendSuccessResponse($connection_row, 'Connection disconnected!');
    }

    public function teamDisconnect(Request $request)
    {
        $userActiveTeam = Generic::getActiveTeamId();

        $blockTeam = $this->teamRepository->findOneByProperties([
            "team_id" => $request->team_id
        ]);

        $fromTeamDisconnect = $blockTeam->sentRequest()->detach($userActiveTeam);
        $toTeamDisconnect = $blockTeam->receivedRequest()->detach($userActiveTeam);

        if($fromTeamDisconnect || $toTeamDisconnect){
            return $this->sendSuccessResponse([], 'Connection disconnected!');
        }

        return $this->sendErrorResponse("Team Connection not found");
    }


}
