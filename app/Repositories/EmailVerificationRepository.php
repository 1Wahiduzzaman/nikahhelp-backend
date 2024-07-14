<?php

namespace App\Repositories;

use App\Models\VerifyUser;

/**
 * Class UserRepository
 */
class EmailVerificationRepository extends BaseRepository
{
    protected $modelName = VerifyUser::class;

    /**
     * UserRepository constructor.
     */
    public function __construct(VerifyUser $model)
    {
        $this->model = $model;
    }
}
