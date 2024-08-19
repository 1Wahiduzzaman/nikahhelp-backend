<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Models\Chat;
use App\Models\ConnectedTeamLastSeen;
use App\Models\Generic;
use App\Models\Message;
use App\Models\OwnTeamLastSeen;
use App\Models\SupportChat;
use App\Models\SupportMessage;
use App\Models\Team;
use App\Models\TeamChat;
use App\Models\TeamConnection;
use App\Models\TeamMember;
use App\Models\TeamMessage;
use App\Models\TeamPrivateChat;
use App\Models\TeamToTeamMessage;
use App\Models\TeamToTeamPrivateMessage;
use App\Repositories\TeamMemberRepository;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use App\Traits\CrudTrait;
use App\Transformers\TeamTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageService extends ApiBaseService
{
    use CrudTrait;

    protected \App\Repositories\UserRepository $userRepository;

    protected \App\Repositories\TeamMemberRepository $teamMemberRepository;

    protected \App\Repositories\TeamRepository $teamRepository;

    protected \App\Transformers\TeamTransformer $teamTransformer;

    protected $team_id;

    /**
     * TeamService constructor.
     */
    public function __construct(TeamRepository $teamRepository, TeamTransformer $teamTransformer, TeamMemberRepository $teamMemberRepository, UserRepository $userRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->teamTransformer = $teamTransformer;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->userRepository = $userRepository;
    }

    public function connectedTeamData($request)
    {
        $active_team_id = (new Generic())->getActiveTeamId();
        $data = TeamConnection::with([
            'from_team' => function ($t1) {
                $t1->with(['team_members' => function ($qq) {
                    $qq->with(['user' => function ($q) {
                        $q->select(['id', 'full_name', 'is_verified', 'status', 'stripe_id', 'account_type']);
                        $q->with(
                            [
                                'candidate_info' => function ($q1) {
                                    $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                                },
                            ])->with(['representative_info' => function ($q2) {
                                $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            }]);
                        // ->with(['block_list' => function($q3){
                        //     $q3->where('user_id','<>', 'id');
                        // }]);
                    }]);
                }])->select('id', 'team_id', 'name', 'description', 'member_count', 'status', 'logo', 'created_at', 'updated_at', 'created_by');
            }, 'to_team' => function ($t2) {
                $t2->with(['team_members' => function ($qq) {
                    $qq->with(['user' => function ($q) {
                        $q->select(['id', 'full_name', 'is_verified', 'status', 'stripe_id', 'account_type']);
                        $q->with(
                            [
                                'candidate_info' => function ($q1) {
                                    $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                                },
                            ])->with(['representative_info' => function ($q2) {
                                $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            }]);
                    }]);
                }])->select('id', 'team_id', 'name', 'description', 'member_count', 'status', 'logo', 'created_at', 'updated_at', 'created_by');
            },
        ])
            ->with(['team_chat' => function ($q) {
                $q->with('last_message');
            }])
            ->with('team_private_chat')
            ->where('connection_status', '1') //added by Raz
            ->where((function ($query) {
                $active_team_id = (new Generic())->getActiveTeamId();
                $query->where(['from_team_id' => $active_team_id])
                    ->orWhere(['to_team_id' => $active_team_id]);
            }))
            ->get();

        return $this->sendSuccessResponse($data, 'Data fetched Successfully!');
    }

    /**
     * Update resource
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function report($request)
    {
        $team_id = $request->team_id;
        $team = $this->teamRepository->findOneByProperties([
            'team_id' => $team_id,
        ]);

        if (! $team) {
            return $this->sendErrorResponse('Team not found.', [], HttpStatusCode::NOT_FOUND->value);
        }

        $team_row_id = $team->id;

        // SQL query explanation: We need to join teams table twice with alias to Get From Team and To Team information
        // Team member information is needed to find out the candidates in both teams.
        // Candidate information table is joined to get the candidate data
        // If user row id is match with to_team then from_team candidate data will be selected and vice varsa as user wants to
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
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND->value);
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
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND->value);
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
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND->value);
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
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND->value);
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
                    $query->where('TCon.connection_status', '=', $connection_status)
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
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND->value);
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
                    $query->where('TCon.connection_status', '=', $connection_status)
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
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::NOT_FOUND->value);
        }

        $they_declined = $this->formatImageUrls($they_declined);

        $data = [];
        $data['connected_teams'] = $connected_teams;
        $data['request_received'] = $request_received;
        $data['request_sent'] = $request_sent;
        $data['we_declined'] = $we_declined;
        $data['they_declined'] = $they_declined;

        return $this->sendSuccessResponse($data, 'Data fetched successfully!');
    }

    public function formatImageUrls($dataArray)
    {
        for ($i = 0; $i < count($dataArray); $i++) {
            if (! empty($dataArray[$i]->candidate_image)) {
                $dataArray[$i]->candidate_image = url('storage/'.$dataArray[$i]->candidate_image);
            }
        }

        return $dataArray;
    }

    /**
     * Determine role for new team member
     *
     * @return Str
     */
    public function getRoleForNewTeamMember(int $user_id): string
    {

        $getUserType = $this->userRepository->findOneByProperties(
            ['id' => $user_id]
        );

        if ($getUserType->account_type == 1) {
            // Check if the user is a candidate in any team
            $checkCandidate = $this->teamMemberRepository->findOneByProperties([
                'user_id' => $user_id,
                'user_type' => 'Candidate',
            ]);

            if (! $checkCandidate) {
                // if No join as Candidate
                return 'Candidate';

            }

            // Join as Representative
            return 'Representative';
        } elseif ($getUserType->account_type == 3) {
            // Join as Matchmaker
            return 'Matchmaker';
        } else {
            // Join as Representative
            return 'Representative';
        }

        // Join as Representative
        return 'Representative';
    }

    /**
     * Get Team list
     */
    public function getTeamList(array $data): JsonResponse
    {
        $user_id = Auth::id();

        try {
            $active_team = TeamMember::where('user_id', $user_id)
                ->where('status', 1)
                ->first();
            $active_team_id = isset($active_team) ? $active_team->team_id : 0;
            $team_infos = Team::with(['team_members' => function ($query) {
                $query->select('team_id', 'user_id')
                    ->with(['user' => function ($q) {
                        $q->select(['id', 'full_name', 'is_verified', 'status', 'stripe_id', 'account_type']);
                        $q->with(
                            [
                                'candidate_info' => function ($q1) {
                                    $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                                },
                            ]
                        )->with(['representative_info' => function ($q2) {
                            $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                        }]);
                    }])
                    ->with('last_message');  //last message from messages table
            }])
                ->with('last_group_message')  // last message from team_messages table
                ->where('id', $active_team_id)
                ->where('status', 1)
                ->first();
            $count = 0;
            if (isset($team_infos)) {
                // $team_infos->logo = isset($team_infos->logo) ? env('IMAGE_SERVER') .'/'. $team_infos->logo : '';
                // $team_infos->base_image_url = @env('IMAGE_SERVER');
                // count unread
                if (isset($team_infos->team_members)) {
                    $count = $team_infos->team_members->filter(function ($item, $key) {
                        return (isset($item->last_message['seen']) && $item->last_message['seen'] == 0) || (isset($item->last_message['seen']) && $item->last_message['seen'] == null);
                    })->count();
                    if ((isset($team_infos->last_group_message->seen)
                    && isset($team_infos->last_group_message->seen) == 0) ||
                    isset($team_infos->last_group_message->seen) == null) {
                        $count += 1;
                    }
                    $team_infos->unread_notification_count = $count;
                }

                return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
            } else {
                return $this->sendErrorResponse('No Result Found!');
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * For One to One Chat
     */
    public function storeChatData($request_data)
    {
        try {
            $sender = $request_data->sender;
            $receiver = $request_data->receiver;
            $this->receiver = $receiver;

            $user_id = Auth::id();
            $is_friend = Chat::where(['sender' => $user_id, 'receiver' => $receiver])
                ->orWhere(function ($q) {
                    $receiver = $this->receiver;
                    $user_id = Auth::id();
                    $q->where(['sender' => $receiver, 'receiver' => $user_id]);
                })
                ->first();
            if (! $is_friend) {
                $cm = new Chat();
                $cm->team_id = $request_data->team_id;
                $cm->sender = $sender;
                $cm->receiver = $receiver;
                if ($cm->save()) {
                    $md = new Message();
                    $md->team_id = $request_data->team_id;
                    $md->chat_id = $cm->id;
                    $md->sender = $request_data->sender;
                    $md->receiver = $request_data->receiver;
                    $md->body = $request_data->message;
                    if ($md->save()) {
                        return $this->sendSuccessResponse([], 'Message Sent Successfully!');
                    } else {
                        return $this->sendErrorResponse('Something went Wrong!Please try again.');
                    }
                }
            } else {
                $md = new Message();
                $md->team_id = $request_data->team_id;
                $md->chat_id = $is_friend->id;
                $md->sender = $request_data->sender;
                $md->receiver = $request_data->receiver;
                $md->body = $request_data->message;
                if ($md->save()) {
                    return $this->sendSuccessResponse([], 'Message Sent Successfully!');
                } else {
                    return $this->sendErrorResponse('Something went Wrong!Please try again.');
                }
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * For Team To Team Chat
     *  Connected Tab
     */
    public function storeTeam2TeamChatData($request_data)
    {
        try {
            $to_team_id = $request_data->to_team_id;
            $team_connection_id = $request_data->team_connection_id;
            $sender = $request_data->sender;

            $user_id = Auth::id();
            $active_team = TeamMember::where('user_id', $user_id)
                ->where('status', 1)
                ->first();
            $active_team_id = isset($active_team) ? $active_team->team_id : 0;
            $from_team_id = $active_team_id;
            // $is_friend = TeamChat::where('from_team_id', $active_team_id)
            // ->orWhere('to_team_id', $active_team_id)
            // ->first();
            $is_friend = TeamChat::where(function ($query) use ($active_team_id, $to_team_id) {
                $query->where('from_team_id', $active_team_id)
                    ->where('to_team_id', $to_team_id);
            })->orWhere(function ($query) use ($active_team_id, $to_team_id) {
                $query->where('from_team_id', $to_team_id)
                    ->where('to_team_id', $active_team_id);
            })->first();

            if (! $is_friend) {
                $cm = new TeamChat();
                $cm->from_team_id = $from_team_id;
                $cm->to_team_id = $to_team_id;
                $cm->team_connection_id = $team_connection_id;
                $cm->sender = $sender;
                if ($cm->save()) {
                    $md = new TeamToTeamMessage();
                    $md->team_chat_id = $cm->id;
                    $md->sender = $cm->sender;
                    $md->from_team_id = $from_team_id;
                    $md->to_team_id = $request_data->to_team_id;
                    $md->body = $request_data->message;
                    if ($md->save()) {
                        return $this->sendSuccessResponse($md, 'Message Sent Successfully!');
                    } else {
                        return $this->sendErrorResponse('Something went Wrong!Please try again.');
                    }
                }
            } else {
                $md = new TeamToTeamMessage();
                $md->team_chat_id = $is_friend->id;
                $md->sender = $user_id;
                $md->from_team_id = $from_team_id;
                $md->to_team_id = $request_data->to_team_id;
                $md->body = $request_data->message;
                if ($md->save()) {
                    return $this->sendSuccessResponse($md, 'Message Sent Successfully!');
                } else {
                    return $this->sendErrorResponse('Something went Wrong!Please try again.');
                }
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public $receiver;

    public function storePrivateChatData($request_data)
    {
        try {
            $from_team_id = $request_data->from_team_id;
            $to_team_id = $request_data->to_team_id;
            $this->receiver = $request_data->receiver;

            //
            $this->from_team_id = $from_team_id;
            $this->to_team_id = $to_team_id;
            $user_id = Auth::id();
            $active_team = TeamMember::where('user_id', $user_id)
                ->where('status', 1)
                ->first();
            $active_team_id = isset($active_team) ? $active_team->team_id : 0;

            $is_friend = TeamPrivateChat::where([
                'from_team_id' => $from_team_id, 'to_team_id' => $to_team_id,
                'sender' => $user_id, 'receiver' => $this->receiver,
            ])
                ->orWhere(function ($q) {
                    $user_id = Auth::id();
                    $from_team_id = $this->from_team_id;
                    $to_team_id = $this->to_team_id;
                    $q->where([
                        'from_team_id' => $to_team_id, 'to_team_id' => $from_team_id,
                        'sender' => $this->receiver, 'receiver' => $user_id,
                    ]);
                })
                ->where('is_friend', 1)
                ->first();
            if (! $is_friend) {
                //
                // $cm = new TeamPrivateChat();
                // $cm->from_team_id = $from_team_id;
                // $cm->to_team_id = $to_team_id;
                // $cm->sender = $user_id;
                // $cm->receiver = $this->receiver;
                // if($cm->save()) {
                //     $md = new TeamToTeamPrivateMessage();
                //     $md->team_private_chat_id = $cm->id;
                //     $md->sender = $user_id;
                //     $md->receiver = $request_data->receiver;
                //     $md->from_team_id = $request_data->from_team_id;
                //     $md->to_team_id = $request_data->to_team_id;
                //     $md->body = $request_data->message;
                //     if($md->save()) {
                //         return $this->sendSuccessResponse([], 'Message Sent Successfully!');
                //     } else {
                //         return $this->sendErrorResponse('Something went Wrong!Please try again.');
                //     }
                // }
                return $this->sendErrorResponse('Sorry! Yo are not allowed to chat untill accepted the request');
            } else {
                $md = new TeamToTeamPrivateMessage();
                $md->team_private_chat_id = $is_friend->id;
                $md->sender = $user_id;
                $md->receiver = $request_data->receiver;
                $md->from_team_id = $request_data->from_team_id;
                $md->to_team_id = $request_data->to_team_id;
                $md->body = $request_data->message;
                if ($md->save()) {
                    return $this->sendSuccessResponse([], 'Message Sent Successfully!');
                } else {
                    return $this->sendErrorResponse('Something went Wrong!Please try again.');
                }
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    // End eonnected

    //Private Chat  Request

    public function createTeamChatAsFriend($request_data)
    {
        try {
            $team_connection_id = $request_data->team_connection_id;
            $to_team_id = $request_data->to_team_id;
            $this->sender = $request_data->sender;    // Candidate of team 1
            $this->receiver = $request_data->receiver;    // Candidate of team 2
            $type = $request_data->type;
            if ($type == 0) {
                $user_id = Auth::id();
                $from_team_id = (new Generic())->getActiveTeamId();

                $this->from_team_id = $from_team_id;
                $this->to_team_id = $to_team_id;

                //Check already Friend
                $is_friend = TeamPrivateChat::where('team_connection_id', $team_connection_id)
                    ->where([
                        'from_team_id' => $from_team_id, 'to_team_id' => $to_team_id,
                        'sender' => $this->sender, 'receiver' => $this->receiver,
                    ])
                    ->orWhere(function ($q) {
                        $from_team_id = $this->from_team_id;
                        $to_team_id = $this->to_team_id;
                        $q->where([
                            'from_team_id' => $to_team_id, 'to_team_id' => $from_team_id,
                            'sender' => $this->receiver, 'receiver' => $this->sender,
                        ]);
                    })
                    ->first();
                if (! $is_friend) {
                    $cm = new TeamPrivateChat();
                    $cm->team_connection_id = $team_connection_id;
                    $cm->from_team_id = $from_team_id;
                    $cm->to_team_id = $to_team_id;
                    $cm->sender = $this->sender;
                    $cm->receiver = $this->receiver;
                    if ($cm->save()) {
                        return $this->sendSuccessResponse([], 'Private Chat requested Successfully!');
                    } else {
                        return $this->sendErrorResponse('Something went Wrong!Please try again.');
                    }
                } else {
                    return $this->sendErrorResponse('Already Requested!');
                }
            } elseif ($type == 1) {
                $team_private_chat_id = $request_data->team_private_chat_id;
                TeamPrivateChat::where('id', $team_private_chat_id)->update(['is_friend' => 1]);

                return $this->sendSuccessResponse([], 'Private Chat Accepted Successfully!');
            } else {
                $team_private_chat_id = $request_data->team_private_chat_id;
                TeamPrivateChat::where('id', $team_private_chat_id)->delete();

                return $this->sendSuccessResponse([], 'Private Chat Rejected Successfully!');
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function getAllPrivateChatRequest()
    {
        try {
            $active_team_id = (new Generic())->getActiveTeamId();
            $data = TeamPrivateChat::with(['private_sender_data' => function ($q) {
                $q->select(['id', 'full_name', 'email', 'is_verified', 'status', 'stripe_id', 'account_type']);
                $q->with(
                    [
                        'candidate_info' => function ($q1) {
                            $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                        },
                    ])->with(['representative_info' => function ($q2) {
                        $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                    }]);
            }])
                ->with(['private_receiver_data' => function ($q) {
                    $q->select(['id', 'full_name', 'email', 'is_verified', 'status', 'stripe_id', 'account_type']);
                    $q->with(
                        [
                            'candidate_info' => function ($q1) {
                                $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            },
                        ])->with(['representative_info' => function ($q2) {
                            $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                        }]);
                }])
                ->where('from_team_id', $active_team_id)
                ->orWhere('to_team_id', $active_team_id)
                ->get();

            return $this->sendSuccessResponse($data, 'All Chat request fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    //store Group message
    public function storeTeamChatData($request_data)
    {
        try {
            $md = new TeamMessage();
            $md->team_id = $request_data->team_id;
            $md->sender = $request_data->sender;
            $md->body = $request_data->message;
            if ($md->save()) {
                return $this->sendSuccessResponse($md, 'Message Sent Successfully!');
            } else {
                return $this->sendErrorResponse('Something went Wrong!Please try again.');
            }

            //get team members
            // $team_members = TeamMember::select('user_id')
            //     ->where('team_id', $request_data->team_id)
            //     ->where('status', 1)
            //     ->get();

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Recent | Single and Group chat history with last messgae
     */
    public function chatHistory(array $data): JsonResponse
    {
        $user_id = self::getUserId();
        try {
            $active_team = TeamMember::where('user_id', $user_id)
                ->where('status', 1)
                ->first();

            $active_team_id = isset($active_team) ? $active_team->team_id : 0;
            $this->team_id = $active_team_id;

            $chats = Chat::select('*')
                ->with(['last_message' => function ($query) {
                    $query->where('team_id', $this->team_id);
                }])
                ->with(['sender_data' => function ($q) {
                    $q->select(['id', 'full_name', 'email', 'is_verified', 'status', 'stripe_id', 'account_type']);
                    $q->with(
                        [
                            'candidate_info' => function ($q1) {
                                $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            },
                        ])->with(['representative_info' => function ($q2) {
                            $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                        }]);
                }])
                ->with(['receiver_data' => function ($q) {
                    $q->select(['id', 'full_name', 'email', 'is_verified', 'status', 'stripe_id', 'account_type']);
                    $q->with(
                        [
                            'candidate_info' => function ($q1) {
                                $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            },
                        ])->with(['representative_info' => function ($q2) {
                            $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                        }]);
                }])
                ->where('team_id', $active_team_id)
                ->where(function ($q) {
                    $user_id = Auth::id();
                    $q->where('sender', $user_id)
                        ->orWhere('receiver', $user_id);
                })
                ->get();
            $result = [];
            $count = 0;
            foreach ($chats as $key => $item) {
                if ($user_id == $item->sender) {
                    $result[$key]['user'] = $item->receiver_data;
                    $result[$key]['last_message'] = $item->last_message;
                } else {
                    $result[$key]['user'] = $item->sender_data;
                    $result[$key]['last_message'] = $item->last_message;
                }
                if (isset($item->last_message->seen) && $item->last_message->seen == 0) {
                    $count++;
                }
            }
            //Get Group Message
            $g_msg = TeamMessage::with(['team' => function ($q) {
                $q->with(['team_members' => function ($qq) {
                    $qq->with(['user' => function ($q) {
                        $q->select(['id', 'full_name', 'email', 'is_verified', 'status', 'stripe_id', 'account_type']);
                        $q->with(
                            [
                                'candidate_info' => function ($q1) {
                                    $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                                },
                            ])->with(['representative_info' => function ($q2) {
                                $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            }]);
                    }]);
                }]);
            }])
                ->where('team_id', $active_team_id)
                ->orderBy('created_at', 'DESC')
                ->first();

            if ((isset($g_msg->seen) && $g_msg->seen == 0) || (isset($g_msg->seen) && $g_msg->seen == null)) {
                $count++;
            }

            //Get Connected Group Message
            $connected_team_msgs = TeamChat::with(['from_team' => function ($q) {
                $q->with('team_members');
            }])
                ->with(['to_team' => function ($q1) {
                    $q1->with(['team_members' => function ($qq) {
                        $qq->with(['user' => function ($q) {
                            $q->select(['id', 'full_name', 'email', 'is_verified', 'status', 'stripe_id', 'account_type']);
                            $q->with(
                                [
                                    'candidate_info' => function ($q1) {
                                        $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                                    },
                                ])->with(['representative_info' => function ($q2) {
                                    $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                                }]);
                        }]);
                    }]);
                }])->with('last_message')
                ->where('from_team_id', $active_team_id)
                ->orWhere('to_team_id', $active_team_id)
                ->get();

            foreach ($connected_team_msgs as $connected_team_msg) {
                if ((isset($connected_team_msg->seen) && $connected_team_msg->seen == 0) ||
                (isset($connected_team_msg->seen) && $connected_team_msg->seen == null)) {
                    $count++;
                }
            }
            //dd($g_msg);
            //$result['g_msg'] = $g_msg;

            // Private Chat
            $private_chat = TeamPrivateChat::select('*')
                ->with(['private_receiver_data' => function ($q) {
                    $q->select(['id', 'full_name', 'email', 'is_verified', 'status', 'stripe_id', 'account_type']);
                    $q->with(
                        [
                            'candidate_info' => function ($q1) {
                                $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            },
                        ])->with(['representative_info' => function ($q2) {
                            $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                        }]);
                }])
                ->with(['private_sender_data' => function ($q) {
                    $q->select(['id', 'full_name', 'email', 'is_verified', 'status', 'stripe_id', 'account_type']);
                    $q->with(
                        [
                            'candidate_info' => function ($q1) {
                                $q1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            },
                        ])->with(['representative_info' => function ($q2) {
                            $q2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                        }]);
                }])
                ->with(['last_private_message' => function ($query) {
                    $query->where('sender', Auth::id());
                    $query->orwhere('receiver', Auth::id());
                }])
                ->where('from_team_id', $active_team_id)
                ->orWhere('to_team_id', $active_team_id)
                ->where(function ($q) {
                    $user_id = Auth::id();
                    $q->where('sender', $user_id)
                        ->orWhere('receiver', $user_id);
                })
                ->get();

            // count unread
            $c = $private_chat->filter(function ($item, $key) {
                return (isset($item->last_private_message['seen']) && $item->last_private_message['seen'] == 0) || (isset($item->last_private_message['seen']) && $item->last_private_message['seen'] == null);
            })->count();

            $count = $count + $c;
            $res = array_merge(
                ['single_chat' => $result],
                ['last_group_msg' => $g_msg],
                ['private_chat' => $private_chat],
                ['connected_team_msgs' => $connected_team_msgs],
                ['count' => $count]
            );

            return $this->sendSuccessResponse($res, 'Data fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function getTeamInformation($teamId)
    {
        if (empty($teamId)) {
            return $this->sendErrorResponse('Team ID is required.', [], HttpStatusCode::VALIDATION_ERROR->value);
        }
        try {
            // Get Team Data
            $team = $this->teamRepository->findOneByProperties([
                'id' => $teamId,
            ]);

            /// Team not found exception throw
            if (! $team) {
                return $this->sendErrorResponse('Team not found.', [], HttpStatusCode::NOT_FOUND->value);
            }

            $team_infos = Team::select('*')
                ->with('team_members', 'team_invited_members', 'created_by')
                ->where('id', '=', $teamId)
                ->get();
            $team_infos[0]['logo'] = url('storage/'.$team_infos[0]['logo']);

            return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function getUsersChatHistory($chat_id = null)
    {
        try {
            $messages = Chat::with(['message_history' => function ($q) {
                $q->with(['sender' => function ($q1) {
                    $q1->select(['full_name', 'id']);
                }]);

                $q->with(['receiver' => function ($q2) {
                    $q2->select(['full_name', 'id']);
                }]);
            }])
                ->where('id', $chat_id)
                ->first();

            return $this->sendSuccessResponse($messages, 'Message fetched Successfully!');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * Chat History for a team
     */
    public function getTeamChatHistory($team_id = null)
    {
        try {
            $messages = TeamMessage::with('sender:id,full_name,is_verified,status,form_type,locked_at,locked_end,account_type,created_at,updated_at')
                ->where('team_id', $team_id)
                ->orderBy('created_at', 'asc')
                ->get();

            return $this->sendSuccessResponse($messages, 'Group Message fetched Successfully!');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public $from_team_id;

    public $to_team_id;

    //Connected Part By raz
    public function getConnectedTeamChatHistory($to_team_id = null)
    {
        try {
            $user_id = Auth::id();
            $active_team = TeamMember::where('user_id', $user_id)
                ->where('status', 1)
                ->first();
            $active_team_id = isset($active_team) ? $active_team->team_id : 0;
            $this->from_team_id = $active_team_id;
            $this->to_team_id = $to_team_id;
            $messages = TeamToTeamMessage::with('sender')
                ->where(['from_team_id' => $active_team_id, 'to_team_id' => $to_team_id])
                ->orWhere(function ($q) {
                    $q->where(['from_team_id' => $this->to_team_id, 'to_team_id' => $this->from_team_id]);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            return $this->sendSuccessResponse($messages, 'Connected Team Messages fetched Successfully!');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function getPrivateChatHistory($chat_id = null)
    {
        try {
            $messages = TeamPrivateChat::with('message_history')
                ->where('id', $chat_id)
                ->first();

            return $this->sendSuccessResponse($messages, 'Message fetched Successfully!');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function updateTeamChatSeen($from_team_id = null, $to_team_id = null)
    {
        try {
            $this->from_team_id = $from_team_id;
            $this->to_team_id = $to_team_id;
            TeamToTeamMessage::where(['from_team_id' => $from_team_id, 'to_team_id' => $to_team_id])
                ->orWhere(function ($q) {
                    $q->where(['from_team_id' => $this->to_team_id, 'to_team_id' => $this->from_team_id]);
                })
                ->update(['seen' => 1]);

            return $this->sendSuccessResponse([], 'Update Successfully!');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function updateOwnTeamChatLastSeen($request)
    {
        $last_seen_msg_id = $request->last_seen_msg_id;
        $team_id = $request->team_id;
        $user_id = Auth::id();

        try {
            $own_team_last_seen = OwnTeamLastSeen::where(['team_id' => $team_id, 'user_id' => $user_id])->first();

            if ($own_team_last_seen) {
                $own_team_last_seen->last_seen_msg_id = $last_seen_msg_id;
                $own_team_last_seen->save();
            } else {
                $own_team_last_seen = new OwnTeamLastSeen();
                $own_team_last_seen->team_id = $team_id;
                $own_team_last_seen->user_id = $user_id;
                $own_team_last_seen->last_seen_msg_id = $last_seen_msg_id;
                $own_team_last_seen->save();
            }

            return $this->sendSuccessResponse([], 'Updated Successfully!');

        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function updateTeamChatLastSeen($request)
    {
        $last_seen_msg_id = $request->last_seen_msg_id;
        $team_chat_id = $request->team_chat_id;
        $user_id = Auth::id();
        try {
            $connected_team_last_seen = ConnectedTeamLastSeen::where(['team_chat_id' => $team_chat_id, 'user_id' => $user_id])->first();

            if ($connected_team_last_seen) {
                $connected_team_last_seen->last_seen_msg_id = $last_seen_msg_id;
                $connected_team_last_seen->save();
            } else {
                $connected_team_last_seen = new ConnectedTeamLastSeen();
                $connected_team_last_seen->team_chat_id = $team_chat_id;
                $connected_team_last_seen->user_id = $user_id;
                $connected_team_last_seen->last_seen_msg_id = $last_seen_msg_id;
                $connected_team_last_seen->save();
            }

            return $this->sendSuccessResponse([], 'Updated Successfully!');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function retrieveOwnTeamChatLastSeen()
    {
        $user_id = Auth::id();
        $team_id = (new Generic())->getActiveTeamId();
        try {
            $own_team_last_seen = OwnTeamLastSeen::where('user_id', $user_id)->first();
            $own_team_last_seen = OwnTeamLastSeen::where('user_id', $user_id)
                ->where('team_id', $team_id)
                ->first();
            if ($own_team_last_seen) {
                return $this->sendSuccessResponse($own_team_last_seen, 'Data fetched Successfully!');
            } else {
                return $this->sendSuccessResponse([], 'Data fetched Successfully!');
            }
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function retrieveTeamChatLastSeen($request)
    {
        // $team_chat_id = $request->team_chat_id;
        $user_id = Auth::id();
        try {
            $connected_team_last_seen = ConnectedTeamLastSeen::where('user_id', $user_id)->get();
            if ($connected_team_last_seen) {
                return $this->sendSuccessResponse($connected_team_last_seen, 'Data fetched Successfully!');
            } else {
                return $this->sendSuccessResponse([], 'Data fetched Successfully!');
            }
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    //Support Chat start here
    /**
     * For One to One Support Chat
     */
    public function storeSupportChatData($request_data)
    {
        try {
            $sender = $request_data->sender;
            $receiver = $request_data->receiver;

            $user_id = Auth::id();
            $is_friend = SupportChat::where('sender', $user_id)
                ->orWhere('receiver', $user_id)
                ->first();
            if (! $is_friend) {
                $cm = new SupportChat();
                $cm->sender = $sender;
                $cm->receiver = $receiver;
                if ($cm->save()) {
                    $md = new SupportMessage();
                    $md->chat_id = $cm->id;
                    $md->sender = $request_data->sender;
                    $md->receiver = $request_data->receiver;
                    $md->body = $request_data->message;
                    if ($md->save()) {
                        return $this->sendSuccessResponse([], 'Message Sent Successfully!');
                    } else {
                        return $this->sendErrorResponse('Something went Wrong!Please try again.');
                    }
                }
            } else {
                $md = new SupportMessage();
                $md->chat_id = $is_friend->id;
                $md->sender = $request_data->sender;
                $md->receiver = $request_data->receiver;
                $md->body = $request_data->message;
                if ($md->save()) {
                    return $this->sendSuccessResponse([], 'Message Sent Successfully!');
                } else {
                    return $this->sendErrorResponse('Something went Wrong!Please try again.');
                }
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function getUsersSupportChatHistory($chat_id = null)
    {
        try {
            $messages = SupportMessage::with(['sender', 'receiver'])
                ->get();

            return $this->sendSuccessResponse($messages, 'Message fetched Successfully!');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function supportChatHistory(array $data): JsonResponse
    {
        $user_id = Auth::id();
        try {
            $chats = SupportChat::select('*')
                ->with('last_message')
                ->with(['sender_data' => function ($q1) {
                    $q1->with(['candidate_info' => function ($s1) {
                        $s1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                    }]);
                    $q1->with(['representative_info' => function ($s1) {
                        $s1->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                    }]);
                }])
                ->with(['receiver_data' => function ($q2) {
                    $q2->with(['candidate_info' => function ($s2) {
                        $s2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                    }]);
                    $q2->with(['representative_info' => function ($s2) {
                        $s2->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                    }]);
                }])
                ->where(function ($q) {
                    $user_id = Auth::id();
                    $q->where('sender', $user_id)
                        ->orWhere('receiver', $user_id);
                })
                ->get();
            $result = [];
            $count = 0;
            foreach ($chats as $key => $item) {
                if ($user_id == $item->sender) {
                    $result[$key]['user'] = $item->receiver_data;
                    $result[$key]['last_message'] = $item->last_message;
                } else {
                    $result[$key]['user'] = $item->sender_data;
                    $result[$key]['last_message'] = $item->last_message;
                }
                if ($item->last_message->seen == 0) {
                    $count++;
                }
            }

            $res = array_merge(
                ['chat_list' => $result],
                ['count' => $count]
            );

            return $this->sendSuccessResponse($res, 'Data fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function seenMessage(Request $request)
    {
        Message::where('id', $request->id)->update(['seen' => 1]);

        return $this->sendSuccessResponse([], 'Message Seen Successfully!');
    }
}
