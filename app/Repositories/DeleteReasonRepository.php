<?php

namespace App\Repositories;

use App\Models\DeleteReason;

/**
 * Class DeleteReasonRepository
 */
class DeleteReasonRepository extends BaseRepository
{
    protected $modelName = DeleteReason::class;

    /**
     * DeleteReasonRepository constructor.
     */
    public function __construct(DeleteReason $model)
    {
        $this->model = $model;
    }
}
