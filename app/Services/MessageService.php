<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Requests\TeamFromRequest;
use App\Models\CandidateInformation;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Team;
use App\Models\TeamChat;
use App\Models\TeamMember;
use App\Models\TeamMessage;
use App\Models\TeamToTeamMessage;
use App\Models\TeamToTeamPrivateMessage;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\AccessRulesDefinitionService;
use Illuminate\Support\Carbon;
use Stripe\Charge;

class MessageService extends ApiBaseService
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
     * @var TeamTransformer
     */
    protected $teamTransformer;
    protected $team_id;

    /**
     * TeamService constructor.
     *
     * @param TeamRepository $teamRepository
     */
    public function __construct(TeamRepository $teamRepository, TeamTransformer $teamTransformer, TeamMemberRepository $teamMemberRepository, UserRepository $userRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->teamTransformer = $teamTransformer;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->userRepository = $userRepository;
    }

   /**
     * Determine role for new team member
     * @param int $user_id
     * @return Str
     */
    public function getRoleForNewTeamMember(int $user_id): string
    {

        $getUserType=$this->userRepository->findOneByProperties(
            ['id'=>$user_id]
        );

        if($getUserType->account_type==1) {
            // Check if the user is a candidate in any team
            $checkCandidate = $this->teamMemberRepository->findOneByProperties([
                'user_id' => $user_id,
                'user_type' => 'Candidate'
            ]);

            if (!$checkCandidate) {
                // if No join as Candidate
                return "Candidate";

            }

            // Join as Representative
            return "Representative";
        } else if($getUserType->account_type==3) {
            // Join as Matchmaker
            return  "Matchmaker";
        }else{
            // Join as Representative
            return "Representative";
    }

        // Join as Representative
        return "Representative";
    }

    /**
     * Get Team list
     * @param array $data
     * @return JsonResponse
     */
    public function getTeamList(array $data): JsonResponse
    {
        $user_id = Auth::id();        

        try {
            $active_team = TeamMember::where('user_id', $user_id)
            ->where('status', 1)
            ->first();
            $active_team_id = isset($active_team) ? $active_team->team_id : 0;
            $team_infos = Team::
                    with(["team_members" => function($query) {
                            $query->select('team_id', 'user_id')->with('last_message');  //last message from messages table
                    }])
                    ->with('last_group_message')  // last message from team_messages table
                    ->where('id', $active_team_id)
                    ->where('status', 1)
                    ->get();

                for ($i = 0; $i < count($team_infos); $i++) {
                    // logo storage code has a bug. need to solve it first. then will change the location
                    $team_infos[$i]->logo = url('storage/' . $team_infos[$i]->logo);
                }
                return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
    /**
     * For One to One Chat
     */
    public function storeChatData($request_data) {
        try{                        
            $sender = $request_data->sender;
            $receiver = $request_data->receiver;
    
            $user_id = Auth::id();
            $is_friend = Chat::where('sender', $user_id)
            ->orWhere('receiver', $user_id)
            ->first();            
            if(!$is_friend) {
                $cm = new Chat();
                $cm->team_id = $request_data->team_id;
                $cm->sender = $sender;
                $cm->receiver = $receiver;
                if($cm->save()) {
                    $md = new Message();
                    $md->team_id = $request_data->team_id;
                    $md->chat_id = $cm->id;
                    $md->sender = $request_data->sender;
                    $md->receiver = $request_data->receiver;
                    $md->body = $request_data->message;
                    if($md->save()) {
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
                if($md->save()) {
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
    public function storeTeam2TeamChatData($request_data) {
        try{                        
            $from_team_id = $request_data->from_team_id;
            $to_team_id = $request_data->to_team_id;
    
            $user_id = Auth::id();      
            $active_team = TeamMember::where('user_id', $user_id)
            ->where('status', 1)
            ->first();
            $active_team_id = isset($active_team) ? $active_team->team_id : 0;

            $is_friend = TeamChat::where('from_team_id', $active_team_id)
            ->orWhere('to_team_id', $active_team_id)
            ->first();            
            if(!$is_friend) {
                $cm = new TeamChat();                
                $cm->from_team_id = $from_team_id;
                $cm->to_team_id = $to_team_id;
                if($cm->save()) {
                    $md = new TeamToTeamMessage();                    
                    $md->team_chat_id = $cm->id;
                    $md->sender = $cm->sender;
                    $md->from_team_id = $request_data->from_team_id;
                    $md->to_team_id = $request_data->to_team_id;
                    $md->body = $request_data->message;
                    if($md->save()) {
                        return $this->sendSuccessResponse([], 'Message Sent Successfully!');
                    } else {
                        return $this->sendErrorResponse('Something went Wrong!Please try again.');
                    }
                }
            } else {
                $md = new TeamToTeamMessage();                
                $md->team_chat_id = $is_friend->id;               
                $md->sender = $user_id;               
                $md->from_team_id = $request_data->from_team_id;
                $md->to_team_id = $request_data->to_team_id;
                $md->body = $request_data->message;
                if($md->save()) {
                    return $this->sendSuccessResponse([], 'Message Sent Successfully!');
                } else {
                    return $this->sendErrorResponse('Something went Wrong!Please try again.');
                }
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }       
    }

    public $receiver;
    public function storePrivateChatData($request_data) {
        try{                        
            $from_team_id = $request_data->from_team_id;
            $to_team_id = $request_data->to_team_id;
            $this->receiver = $request_data->receiver;            
            $user_id = Auth::id();      
            $active_team = TeamMember::where('user_id', $user_id)
            ->where('status', 1)
            ->first();
            $active_team_id = isset($active_team) ? $active_team->team_id : 0;

            $is_friend = TeamChat::where([
                'from_team_id'=> $from_team_id, 'to_team_id' => $to_team_id, 
                'sender' => $user_id, 'receiver' => $this->receiver
                ])            
            ->orWhere(function($q){
                $user_id = Auth::id(); 
                $from_team_id = $this->from_team_id;
                $to_team_id = $this->to_team_id;
                $q->where([
                    'from_team_id'=> $to_team_id, 'to_team_id' => $from_team_id, 
                    'sender' => $this->receiver, 'receiver' => $user_id
                ]);   
            })  
            ->first();            
            if(!$is_friend) {
                $cm = new TeamChat();                
                $cm->from_team_id = $from_team_id;
                $cm->to_team_id = $to_team_id;
                $cm->sender = $user_id;
                $cm->receiver = $this->receiver;
                if($cm->save()) {
                    $md = new TeamToTeamPrivateMessage();                    
                    $md->team_chat_id = $cm->id;
                    $md->sender = $user_id;
                    $md->receiver = $request_data->receiver;
                    $md->from_team_id = $request_data->from_team_id;
                    $md->to_team_id = $request_data->to_team_id;
                    $md->body = $request_data->message;
                    if($md->save()) {
                        return $this->sendSuccessResponse([], 'Message Sent Successfully!');
                    } else {
                        return $this->sendErrorResponse('Something went Wrong!Please try again.');
                    }
                }
            } else {
                $md = new TeamToTeamPrivateMessage();                
                $md->team_chat_id = $is_friend->id;               
                $md->sender = $user_id;                
                $md->receiver = $request_data->receiver;           
                $md->from_team_id = $request_data->from_team_id;
                $md->to_team_id = $request_data->to_team_id;
                $md->body = $request_data->message;
                if($md->save()) {
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
    public function createTeamChatAsFriend($request_data) {
        try{                                    
            $to_team_id = $request_data->to_team_id;
            $status = $request_data->status;
            if($status=='1') {
                $user_id = Auth::id();      
                $active_team = TeamMember::where('user_id', $user_id)
                ->where('status', 1)
                ->first();
                $active_team_id = isset($active_team) ? $active_team->team_id : 0;

                $cm = new TeamChat();                
                $cm->from_team_id = $active_team_id;
                $cm->to_team_id = $to_team_id;
                if($cm->save()) {
                    return $this->sendSuccessResponse([], 'Connection created Successfully!');
                } else {
                    return $this->sendErrorResponse('Something went Wrong!Please try again.');
                }
            }          
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }       
    }

    //store Group message
    public function storeTeamChatData($request_data) {
        try{    
            $md = new TeamMessage();
            $md->team_id = $request_data->team_id;         
            $md->sender = $request_data->sender;                
            $md->body = $request_data->message;
            if($md->save()) {
                return $this->sendSuccessResponse([], 'Message Sent Successfully!');
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
        $user_id = Auth::id();                
        try {
            $active_team = TeamMember::where('user_id', $user_id)
            ->where('status', 1)
            ->first();
            $active_team_id = isset($active_team) ? $active_team->team_id : 0;
            $this->team_id = $active_team_id;
                $chats = Chat::select('*')    
                ->with(['last_message'=> function($query){                    
                        $query->where('team_id', $this->team_id);
                }])                  
                ->with('sender_data')
                ->with('receiver_data')
                ->where('team_id', $active_team_id)   
                ->where(function($q){
                    $user_id = Auth::id(); 
                    $q->where('sender' , $user_id)
                      ->orWhere('receiver', $user_id);   
                })                                                         
                ->get();     
                $result = [];
                foreach($chats as $key=>$item) {                    
                    if($user_id==$item->sender){
                        $result[$key]['user'] = $item->receiver_data;
                        $result[$key]['last_message'] = $item->last_message;
                    } else {
                        $result[$key]['user'] = $item->sender_data;
                        $result[$key]['last_message'] = $item->last_message;
                    }
                }
                //Get Group Message
                $g_msg = TeamMessage::with("team")
                    ->where('team_id', $active_team_id)   
                    ->orderBy('created_at' , 'DESC')
                    ->first();     
                    //dd($g_msg);       
                //$result['g_msg'] = $g_msg;   

                // Private Chat 
                $private_chat = TeamChat::select('*')  
                ->with('private_receiver_data')  
                ->with(['last_private_message'=> function($query){                                      
                    $query->where('sender', Auth::id());
                    $query->orwhere('receiver', Auth::id());
                }])                                  
                ->where('from_team_id', $active_team_id)   
                ->orWhere('to_team_id', $active_team_id)
                ->where(function($q){
                    $user_id = Auth::id(); 
                    $q->where('sender' , $user_id)
                      ->orWhere('receiver', $user_id);   
                })                                                                          
                ->get();     

                $res = array_merge(['single_chat' => $result], ['last_group_msg' => $g_msg], ['private_chat' => $private_chat]);    
                return $this->sendSuccessResponse($res, 'Data fetched Successfully!');
            }
        catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function getTeamInformation($teamId)
    {
        if (empty($teamId)) {
            return $this->sendErrorResponse('Team ID is required.', [], HttpStatusCode::VALIDATION_ERROR);
        }
        try {
            // Get Team Data
            $team = $this->teamRepository->findOneByProperties([
                "id" => $teamId
            ]);

            /// Team not found exception throw
            if (!$team) {
                return $this->sendErrorResponse('Team not found.', [], HttpStatusCode::NOT_FOUND);
            }

            $team_infos = Team::select("*")
                ->with("team_members", 'team_invited_members','created_by')
                ->where('id', '=', $teamId)
                ->get();
            $team_infos[0]['logo'] = url('storage/' . $team_infos[0]['logo']);
            return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function getUsersChatHistory($chat_id = null) {
        try{
            $messages = Chat::with('message_history')
            ->where('id', $chat_id)            
            ->get();
            return $this->sendSuccessResponse($messages, 'Message fetched Successfully!');
        } catch(Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }        
    }
    /**
     * Chat History for a team
     */
    public function getTeamChatHistory($team_id = null) {
        try{
            $messages = TeamMessage::with('sender')
            ->where('team_id', $team_id) 
            ->orderBy('created_at', 'asc')          
            ->get();
            return $this->sendSuccessResponse($messages, 'Group Message fetched Successfully!');
        } catch(Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }        
    }
    public $from_team_id, $to_team_id;
    //Connected Part By raz
    public function getConnectedTeamChatHistory($from_team_id = null, $to_team_id = null) {
        try{
            $this->from_team_id = $from_team_id;
            $this->to_team_id = $to_team_id;
            $messages = TeamToTeamMessage::with('sender')
            ->where(['from_team_id'=> $from_team_id, 'to_team_id' => $to_team_id]) 
            ->orWhere(function($q){               
                $q->where(['from_team_id'=> $this->to_team_id, 'to_team_id' => $this->from_team_id]);                  
            })                
            ->orderBy('created_at', 'asc')          
            ->get();
            return $this->sendSuccessResponse($messages, 'Connected Team Messages fetched Successfully!');
        } catch(Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }        
    }

    public function getPrivateChatHistory($chat_id = null) {
        try{
            $messages = TeamChat::with('message_history')
            ->where('id', $chat_id)            
            ->get();
            return $this->sendSuccessResponse($messages, 'Message fetched Successfully!');
        } catch(Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }        
    }

}
