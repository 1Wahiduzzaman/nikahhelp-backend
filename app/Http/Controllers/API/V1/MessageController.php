<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Generic;
use App\Models\Message;
use App\Models\TeamMessage;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * @var MessageService
     */
    public $messageService;


    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }
    /**
     * Send Message for storing data into db
     */
    public function sendMessage(Request $request) {       
        return $this->messageService->storeChatData($request);        
    }

    public function seenMessage(Request $request) {       
        return $this->messageService->seenMessage($request);        
    }

    public function sendMessageToTeam(Request $request) {       
        return $this->messageService->storeTeamChatData($request);        
    }     
    /**
     * List user teams.
     *
     * @param Request $request
     *
     */
    public function teamChatList(Request $request)
    {        
        return $this->messageService->getTeamList($request->all());
    }

    /**
     * List user teams.
     *Recent Chat History with Last Message
     * @param Request $request
     *
     */
    public function chatHistory(Request $request)
    {        
        return $this->messageService->chatHistory($request->all());
    }
    /**
     * Individual chat history | get all chat by group or a user
     */
    public function individualChatHistory(Request $request)
    {    
        $type = $request->type;        
        $user_id = Auth::id();
        if($type =='single') {
            $chat_id = $request->chat_id;    
            // Manage Seen            
            Message::where('team_id', (new Generic())->getActiveTeamId())
            ->where('chat_id', $chat_id)
            ->where('receiver', $user_id)
            ->update(['seen' =>1]);        
            // Manage Seen
            return $this->messageService->getUsersChatHistory($chat_id);
        } else {
            $team_id = $request->team_id;

            // Manage Seen            
            TeamMessage::where('team_id', (new Generic())->getActiveTeamId())                        
            ->update(['seen' =>1]);        
            // Manage Seen

            return $this->messageService->getTeamChatHistory($team_id);
        }      
    }

    //Connected Part By Raz
    public function report(Request $request)
    {        
        return $this->messageService->report($request);
    }

    public function connectedTeamData(Request $request)
    {        
        return $this->messageService->connectedTeamData($request);
    }

    public function sendMessageTeamToTeam(Request $request) {       
        return $this->messageService->storeTeam2TeamChatData($request);        
    }
    public function sendPrivateMessage(Request $request) {       
        return $this->messageService->storePrivateChatData($request);        
    }    
    public function privateChatRequestAcceptOrReject(Request $request) {       
        return $this->messageService->createTeamChatAsFriend($request);        
    }

    public function getAllPrivateChatRequest() {       
        return $this->messageService->getAllPrivateChatRequest();        
    }
        
    public function connectedTeamChatHistory(Request $request) {                  
        $to_team_id = $request->to_team_id;
        return $this->messageService->getConnectedTeamChatHistory($to_team_id);        
    }  
    
    public function privateChatHistory(Request $request) {                  
        return $this->messageService->getPrivateChatHistory($request->team_private_chat_id);        
    }  

    public function teamChatSeen(Request $request) {                  
        return $this->messageService->updateTeamChatSeen($request->from_team_id, $request->to_team_id);        
    }      



    //Suppor Chat Start here
    public function sendMessageToSupport(Request $request) {       
        return $this->messageService->storeSupportChatData($request);        
    }

    public function individualSupportChatHistory(Request $request)
    {            
        $chat_id = $request->chat_id;        
        return $this->messageService->getUsersSupportChatHistory($chat_id);  
    }

    public function supportChatHistory(Request $request)
    {        
        return $this->messageService->supportChatHistory($request->all());
    }
}
