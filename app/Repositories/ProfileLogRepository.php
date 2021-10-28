<?php

namespace App\Repositories;

use App\Http\Requests\UserNotificationRequest;
use App\Models\ProfileLog;
use App\Http\Requests\DeviceTokenRequest;
use Illuminate\Http\Request;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class ProfileLogRepository extends BaseRepository
{
    protected $modelName = ProfileLog::class;

    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];


    /**
     * UserRepository constructor.
     *
     * @param ProfileLog $model
     */
    public function __construct(ProfileLog $model)
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
