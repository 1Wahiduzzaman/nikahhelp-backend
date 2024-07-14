<?php

namespace App\Helpers;

use App\Models\Notification;
use Exception;

/**
 * Class ApiBaseService
 */
class Notificationhelpers
{
    public static function add($massage = null, $type = null, $team = null, $user = null)
    {
        try {
            $save = new Notification();
            $save->data = $massage ?? null;
            $save->type = $type ?? 'team';
            $save->team_id = $team ?? null;
            $save->user_id = $user ?? null;
            $save->save();

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
