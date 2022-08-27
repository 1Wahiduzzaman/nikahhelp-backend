<?php

namespace App\Repositories;

use App\Http\Requests\UserNotificationRequest;
use App\Models\TicketSubmission;
use App\Http\Requests\DeviceTokenRequest;

/**
 * Class TicketRepository
 *
 * @package App\Repositories
 */
class TicketRepository extends BaseRepository
{
    protected $modelName = TicketSubmission::class;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user',
        'user_id',
        'issue',
        'issue_type',
        'screen_shot_path'
    ];


    /**
     * UserRepository constructor.
     *
     * @param TicketSubmission $model
     */
    public function __construct(TicketSubmission $model)
    {
        $this->model = $model;
    }

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

}
