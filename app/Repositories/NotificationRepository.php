<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\BaseRepository;

/**
 * Class NotificationRepository
 * @package App\Repositories
 * @version June 2, 2021, 1:05 pm UTC
*/

class NotificationRepository extends BaseRepository
{
    protected $modelName = Notification::class;

    /**
     * Notification constructor.
     *
     * @param Notification $model
     */
    public function __construct(Notification $model)
    {
        $this->model = $model;
    }


    public function getAllNotification($request){
        $skip=  $request->get('skip');
        $limit=  $request->get('limit');
       return $notifications = $this->model->get();

    }

}
