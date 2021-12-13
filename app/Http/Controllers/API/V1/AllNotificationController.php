<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AllNotification;

class AllNotificationController extends Controller
{
    // For every cases this will fire
    public function saveNotifications(Request $request){                
        $model = new AllNotification();
        $model->sender = $request->sender;
        $model->receiver = $request->receiver;
        $model->team_id = $request->team_id;
        $model->title = $request->title;
        $model->description = $request->description;
        $model->save();
        return $this->sendSuccessResponse([], 'Data fetched Successfully!');
    }
}
