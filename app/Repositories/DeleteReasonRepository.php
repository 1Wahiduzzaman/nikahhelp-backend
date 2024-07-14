<?php

namespace App\Repositories;

use App\Models\DeleteReason;

/**
 * Class DeleteReasonRepository
 *
 * @package App\Repositories
 */
class DeleteReasonRepository  extends BaseRepository
{
    protected $modelName = DeleteReason::class;

    /**
     * DeleteReasonRepository constructor.
     *
     * @param DeleteReason $model
     */
    public function __construct(DeleteReason $model)
    {
        $this->model = $model;
    }



}
