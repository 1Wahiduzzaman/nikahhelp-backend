<?php

namespace App\Repositories;

use App\Http\Requests\UserNotificationRequest;
use App\Models\VerifyUser;
use App\Http\Requests\DeviceTokenRequest;
use Illuminate\Http\Request;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class EmailVerificationRepository  extends BaseRepository
{
    protected $modelName = VerifyUser::class;

    /**
     * UserRepository constructor.
     *
     * @param VerifyUser $model
     */
    public function __construct(VerifyUser $model)
    {
        $this->model = $model;
    }
}
